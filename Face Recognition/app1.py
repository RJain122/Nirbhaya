import cv2
import os
import numpy as np
import time
from flask import Flask, render_template, Response, jsonify
import MySQLdb
import threading

app = Flask(__name__)

# MySQL Connection (XAMPP)
db = MySQLdb.connect(host="localhost", user="root", passwd="", db="face_recog_db")
cursor = db.cursor()

# Ensure static folder exists
IMAGE_FOLDER = "static/captured_images"
os.makedirs(IMAGE_FOLDER, exist_ok=True)

# Load OpenCV Face Detector
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

camera = None  # Global variable for camera


def start_camera():
    """ Starts the camera in a separate thread """
    global camera
    if camera is None or not camera.isOpened():
        camera = cv2.VideoCapture(0, cv2.CAP_DSHOW)  # Open camera
        time.sleep(2)  # Give time to initialize


def generate_frames():
    """ Streams video feed to the frontend """
    global camera
    start_camera()

    while camera.isOpened():
        success, frame = camera.read()
        if not success:
            break

        # Convert to grayscale for face detection
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(50, 50))

        # Draw rectangles around faces
        for (x, y, w, h) in faces:
            cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)

        _, buffer = cv2.imencode('.jpg', frame)
        frame_bytes = buffer.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

    camera.release()


@app.route('/')
def index():
    return render_template('index.html')


@app.route('/video_feed')
def video_feed():
    """ Route for live video feed """
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')


@app.route('/capture', methods=['POST'])
def capture():
    """ Captures face and saves it in MySQL + static folder """
    global camera
    if camera is None or not camera.isOpened():
        return jsonify({"message": "Camera not available!", "success": False})

    success, frame = camera.read()
    if success:
        image_filename = f"captured_{int(time.time())}.jpg"
        image_path = os.path.join(IMAGE_FOLDER, image_filename)
        cv2.imwrite(image_path, frame)  # Save image to static folder

        # Save to MySQL as BLOB
        try:
            with open(image_path, "rb") as image_file:
                image_data = image_file.read()
                cursor.execute("INSERT INTO faces (image, image_path) VALUES (%s, %s)", (image_data, image_filename))
                db.commit()
            return jsonify({"message": "Face scanned successfully!", "success": True, "image_path": image_path})
        except Exception as e:
            return jsonify({"message": f"Database error: {str(e)}", "success": False})

    return jsonify({"message": "Face scan failed!", "success": False})


def run_flask():
    """ Runs Flask in the background """
    app.run(debug=False, port=5000, host="0.0.0.0", use_reloader=False)


if __name__ == "__main__":
    flask_thread = threading.Thread(target=run_flask, daemon=True)
    flask_thread.start()
    start_camera()  # Automatically starts the camera
