
# 🔒 Nirbhaya Project

**Nirbhaya** is a web platform focused on promoting **women's safety and empowerment**. It offers features like emergency alerts, incident reporting, safe zone mapping, and access to verified resources. The goal is to create a safer and more aware environment for women through technology.

---

## 🚀 Features

* **Emergency Alert** – Share location and send SOS messages to saved contacts.
* **Incident Reporting** – Log incidents safely and anonymously.
* **Safety Resources** – Quick access to helpline numbers, self-defense guides, and safety tips.
* **Safe Zone Map** – Map to show trusted public locations and nearby help centers.
* **Community Support** – Platform for verified posts, discussions, and safety updates.

---

## 🛠️ Tech Stack

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP (if used), MySQL
* **Database:** MySQL (via XAMPP)
* **Deployment:** XAMPP (Localhost)

---

## 📂 How to Run the Project on Localhost using XAMPP

> Follow these steps to set up and run the Nirbhaya project on your local system using XAMPP.

### 🔧 Requirements

* XAMPP installed [Download Here](https://www.apachefriends.org/index.html)
* Web browser (Chrome/Firefox)
* Code editor (VS Code recommended)

---

### 🧾 Steps to Run

1. **Start XAMPP Services:**

   * Open XAMPP Control Panel
   * Start **Apache** and **MySQL**

2. **Place Project in `htdocs`:**

   * Copy your entire Nirbhaya project folder
   * Paste it inside:

     ```
     C:\xampp\htdocs\
     ```

3. **Database Setup (If applicable):**

   * Open browser and go to:

     ```
     http://localhost/phpmyadmin
     ```
   * Create a new database (e.g., `nirbhaya`)
   * Import the provided `.sql` file from the `database/` folder if included

4. **Configure Connection (If using PHP & MySQL):**

   * Open the `config.php` or `db.php` file in your project
   * Set the DB credentials:

     ```php
     $host = 'localhost';
     $user = 'root';
     $password = '';
     $database = 'nirbhaya';
     ```

5. **Run the Project in Browser:**

   * Visit:

     ```
     http://localhost/nirbhaya/
     ```

---

## ✅ Future Enhancements

* Integrate GPS tracking and mobile alerts
* Enable multilingual support
* Build mobile app version (React Native or Flutter)

---


