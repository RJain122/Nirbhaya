import cv2

cap = cv2.VideoCapture(0, cv2.CAP_DSHOW)  # Try changing 0 to 1 or -1 if it fails

if not cap.isOpened():
    print("❌ Error: Could not open webcam!")
else:
    print("✅ Webcam is accessible!")

cap.release()
