<?php
// User.php
// Bevat alle methodes voor gebruikersfunctionaliteit
// geupdated bij Jan Mohamad op 05-10-2026 voor docent R. Wigmans

require_once "Database.php";

class User {
    private $conn;
    public $username;
    public $password;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Methode om een gebruiker te registreren
    public function registerUser($gebruikersnaam, $wachtwoord) {
        try {
            // Controleer of gebruiker al bestaat
            $check = $this->conn->prepare("SELECT * FROM user WHERE gebruikersnaam = :gebruikersnaam");
            $check->bindParam(':gebruikersnaam', $gebruikersnaam);
            $check->execute();

            if ($check->rowCount() > 0) {
                return "Gebruiker bestaat al.";
            }

            // Hash wachtwoord en voeg toe
            $hashedPassword = password_hash($wachtwoord, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO user (gebruikersnaam, wachtwoord) VALUES (:gebruikersnaam, :wachtwoord)");
            $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
            $stmt->bindParam(':wachtwoord', $hashedPassword);
            $stmt->execute();

            return "Registratie succesvol.";
        } catch (PDOException $e) {
            return "Fout bij registratie: " . $e->getMessage();
        }
    }

    // Methode om in te loggen
    public function loginUser() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE gebruikersnaam = :gebruikersnaam");
            $stmt->bindParam(':gebruikersnaam', $this->username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($this->password, $user['wachtwoord'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['gebruikersnaam'];
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    // Methode om uit te loggen
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

//  Nieuw toegevoegd: check of user ingelogd is
    public function isLoggedin() {
        return isset($_SESSION['user_id']);
    }

    // Aangepast: valideer inputvelden inclusief lengte van username (3 - 50 tekens)
    public function validateUser() {
        $errors = [];

        $username = trim($this->username ?? '');

        // 1. Controle op gebruikersnaam (verplicht + lengte tussen 3 en 50 tekens)
        if (empty($username)) {
            $errors[] = "Gebruikersnaam is verplicht.";
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Gebruikersnaam moet tussen 3 en 50 tekens lang zijn.";
        }

        // 2. Controle op wachtwoord
        if (empty($this->password)) {
            $errors[] = "Wachtwoord is verplicht.";
        }

        return $errors;
    }

    // ✅ Nieuw toegevoegd: haal user op
    public function getUser($gebruikersnaam) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE gebruikersnaam = :gebruikersnaam");
        $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Nieuw toegevoegd: toon user info (voor debug of homepagina)
    public function showUser() {
        echo "Gebruikersnaam: " . htmlspecialchars($this->username) . "<br>";
    }
}
?>
