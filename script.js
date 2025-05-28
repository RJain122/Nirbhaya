// Configuration
const CODE_WORD = "help"; // Change this to your desired code word
const RECIPIENT_EMAIL = "aasthajain590@gmail.com"; // Change to your recipient email
const SENDER_EMAIL = "ritsjain2004@gmail.com"; // Change to your sender email
const SENDER_NAME = "Voice Activation System";

// DOM Elements
const toggleBtn = document.getElementById('toggleBtn');
const statusDiv = document.getElementById('status');

// Speech recognition setup
let recognition;
let isListening = false;

// Initialize speech recognition
function initSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (!SpeechRecognition) {
        statusDiv.textContent = "Speech API not supported";
        toggleBtn.disabled = true;
        return;
    }
    
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    
    recognition.onstart = () => {
        isListening = true;
        toggleBtn.classList.add('active');
        statusDiv.textContent = "Listening...";
    };
    
    recognition.onend = () => {
        if (isListening) {
            recognition.start(); // Restart if still enabled
        }
    };
    
    recognition.onerror = (event) => {
        console.error("Speech recognition error", event.error);
        statusDiv.textContent = `Error: ${event.error}`;
        stopListening();
    };
    
    recognition.onresult = (event) => {
        let interimTranscript = '';
        let finalTranscript = '';
        
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalTranscript += transcript;
            } else {
                interimTranscript += transcript;
            }
        }
        
        // Check for code word in both interim and final results
        if (finalTranscript.toLowerCase().includes(CODE_WORD) || 
            interimTranscript.toLowerCase().includes(CODE_WORD)) {
            statusDiv.textContent = "Code word detected!";
            sendEmailNotification();
            stopListening();
        }
    };
}

// Toggle listening state
function toggleListening() {
    if (isListening) {
        stopListening();
    } else {
        startListening();
    }
}

function startListening() {
    if (!recognition) initSpeechRecognition();
    try {
        recognition.start();
    } catch (e) {
        statusDiv.textContent = "Error starting recognition";
        console.error(e);
    }
}

function stopListening() {
    if (recognition) {
        isListening = false;
        recognition.stop();
        toggleBtn.classList.remove('active');
        statusDiv.textContent = "Off";
    }
}

// Send email via Brevo API
async function sendEmailNotification() {
    statusDiv.textContent = "Sending notification...";
    
    try {
        const response = await fetch('send_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                recipient: RECIPIENT_EMAIL,
                sender_email: SENDER_EMAIL,
                sender_name: SENDER_NAME,
                code_word: CODE_WORD
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            statusDiv.textContent = "Notification sent!";
        } else {
            statusDiv.textContent = "Failed to send";
            console.error(result.error);
        }
    } catch (error) {
        statusDiv.textContent = "Connection error";
        console.error('Error:', error);
    }
    
    // Reset after 3 seconds
    setTimeout(() => {
        statusDiv.textContent = "Off";
    }, 3000);
}

// Event listeners
toggleBtn.addEventListener('click', toggleListening);

// Request microphone permission on first click
toggleBtn.addEventListener('click', function init() {
    toggleBtn.removeEventListener('click', init);
    
    // Check for microphone permission
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(() => {
                console.log("Microphone access granted");
            })
            .catch(err => {
                console.error("Microphone access denied", err);
                statusDiv.textContent = "Mic access denied";
                toggleBtn.disabled = true;
            });
    }
}, { once: true });