/* Récupéré élément du DOM  

Fonction getModulsData()
Quand bouton "Créer" préssé récupére 
- Titre 
- Description
- Contenu
- Image

Fonction addModuls()
Après récupération, affichage des données  :

lance une fonction pour ajouter les données dans la base de données en PHP

    Dans page/home.php
    - Création dynamique d'un modal avec les informations (titre, description et image)
    Dans page/profil.php 
    - afficher les données sous forme tableau (Titre, Date, auteur)

Fonction modifModuls()
Quand bouton "Modifier" préssé affichage d'un pop up avec les données pour les modifiers.
Quand boton "Supprimer" préssé suppréssion du moduls de la base données avec un popup de confirmation.

*/

class Moduls {
    constructor(title, description, content, image) {
        this.title          = title;
        this.description    = description;
        this.content        = content;
        this.image          = image;
    }

    getModulsData() {
        const title             = document.getElementById('title').value;
        const description       = document.getElementById('description').value;
        const content           = document.getElementById('content').value;
        const image             = document.getElementById('image').value;
        console.log(title, description, content, image);
        return { title, description, content, image };

        }

    addModuls() {
        const moduls = new Moduls(this.title, this.description, this.content, this.image);
        moduls.addModuls();
    }

}
