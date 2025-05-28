import os
import base64
import cv2
import numpy as np
import MySQLdb
from flask import Flask, request, jsonify

app = Flask(__name__)

# Connect to MySQL (XAMPP)
db = MySQLdb.connect(host="localhost", user="root", passwd="", db="face_recog_db")
cursor = db.cursor()

# Ensure the static folder exists
IMAGE_FOLDER = "static/captured_images"
os.makedirs(IMAGE_FOLDER, exist_ok=True)

# Load OpenCV Face Detector
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

@app.route('/capture', methods=['POST'])
def capture():
    """API to receive and save an image from the frontend"""
    try:
        data = request.json['image']  # Get Base64 Image Data
        image_data = base64.b64decode(data.split(',')[1])  # Decode Base64
        
        # Convert to OpenCV image
        np_arr = np.frombuffer(image_data, np.uint8)
        frame = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)

        # Detect Faces
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(50, 50))

        if len(faces) == 0:
            return jsonify({"message": "No face detected!", "success": False})

        # Save Image to Static Folder
        image_filename = f"captured_{len(faces)}.jpg"
        image_path = os.path.join(IMAGE_FOLDER, image_filename)
        cv2.imwrite(image_path, frame)

        # Save Image to MySQL as BLOB
        with open(image_path, "rb") as image_file:
            image_blob = image_file.read()
            cursor.execute("INSERT INTO faces (image, image_path) VALUES (%s, %s)", (image_blob, image_filename))
            db.commit()

        return jsonify({"message": "Face scanned successfully!", "success": True, "image_path": image_path})

    except Exception as e:
        return jsonify({"message": f"Error: {str(e)}", "success": False})

if __name__ == "__main__":
    app.run(debug=True, port=5000)
