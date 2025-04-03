<?php 
    class User {
        
        private int $id;
        private string $identifiant;
        private string $nom;
        private string $prenom;
        private string $email;
        private string $password;
        private datetime $created_at;
        
        public function __construct($id, $identifiant, $nom, $prenom, $email, $password, $created_at) {
            $this->id = $id;
            $this->identifiant = $identifiant;
            $this->nom = $nom;
            $this->prenom = $prenom;
            $this->email = $email;
            $this->password = $password;
            $this->created_at = $created_at;
        }

        public function getId(): int {
            return $this->id;
        }

        public function getIdentifiant(): string {
            return $this->identifiant;
        }

        public function getNom(): string {
            return $this->nom;
        }

        public function getPrenom(): string {
            return $this->prenom;
        }

        public function getEmail(): string {
            return $this->email;
        }

        public function getCreationDate(): datetime {
            return $this->created_at;
        }

        public function setIdentifiant(string $identifiant): void {
            $this->identifiant = $identifiant;
        }
        
        public function setNom(string $nom): void {
            $this->nom = $nom;
        }
        
        public function setPrenom(string $prenom): void {
            $this->prenom = $prenom;
        }
        
        public function setEmail(string $email): void {
            $this->email = $email;
        }
        
        public function setPassword(string $password): void {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }

    }  