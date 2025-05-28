import cv2
import os
import numpy as np
from flask import Flask, render_template, Response, request, jsonify, redirect, url_for
import MySQLdb
import time

app = Flask(__name__)

# Connect to MySQL (XAMPP)
db = MySQLdb.connect(host="localhost", user="root", passwd="", db="face_recog_db")
cursor = db.cursor()

# Load OpenCV Face Detector
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

camera = cv2.VideoCapture(0, cv2.CAP_DSHOW)  # Open camera at start

def generate_frames():
    global camera
    while True:
        success, frame = camera.read()
        if not success:
            break

        # Convert frame to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

        # Detect faces
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(50, 50))

        # Draw rectangles around faces
        for (x, y, w, h) in faces:
            cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)

        _, buffer = cv2.imencode('.jpg', frame)
        frame_bytes = buffer.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/capture', methods=['POST'])
def capture():
    global camera
    success, frame = camera.read()
    
    if success:
        filename = f"scanned_{int(time.time())}.jpg"
        file_path = os.path.join("static", filename)
        cv2.imwrite(file_path, frame)  # Save Image
        
        # Save to MySQL Database
        with open(file_path, "rb") as image_file:
            image_data = image_file.read()
            cursor.execute("INSERT INTO faces (image) VALUES (%s)", (image_data,))
            db.commit()

        os.remove(file_path)  # Delete after saving

        return jsonify({"success": True, "message": "Face scanned successfully!"})

    return jsonify({"success": False, "message": "Face scan failed. Try again!"})

if __name__ == "__main__":
    app.run(debug=True, port=5000)
