console.log('checkInscription.js');

let isPasswordValid = false;
let isPseudoValid = false;

// Ajout des événements pour la validation en temps réel
document.getElementById('password').addEventListener('input', checkPassword);
document.getElementById('password2').addEventListener('input', checkPassword);
document.getElementById('identifiant').addEventListener('input', checkPseudo);

function updateValidationButton() {
    const valideButton = document.getElementById('valide');
    if (isPasswordValid && isPseudoValid) {
        valideButton.removeAttribute('disabled');
    } else {
        valideButton.setAttribute('disabled', 'disabled');
    }
}

function checkPassword() {
    console.log('checkPassword()');

    let password = document.getElementById('password').value;
    let password2 = document.getElementById('password2').value;
    let messageContainer = document.getElementById('password-message-container');
    let messageContainer2 = document.getElementById('password-message-container2');

    messageContainer.innerHTML = '';
    messageContainer2.innerHTML = '';

    let errors = [];
    let errors2 = [];

    if (password.length > 0) {
        if (password !== password2 && password2.length > 0) {
            errors.push("Les mots de passe ne correspondent pas.");
        }
        if (password.length < 8) {
            errors2.push("Le mot de passe doit contenir au moins 8 caractères.");
        }
        if (!/[A-Z]/.test(password) || !/[a-z]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins une majuscule et une minuscule.");
        }
        if (!/[0-9]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins un chiffre.");
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins un caractère spécial.");
        }

        if (errors.length > 0 || errors2.length > 0) {
            document.getElementById('password').style.borderColor = 'red';
            document.getElementById('password2').style.borderColor = 'red';
            isPasswordValid = false;
        } else {
            document.getElementById('password').style.borderColor = 'green';
            document.getElementById('password2').style.borderColor = 'green';
            isPasswordValid = true;

            let successMessage = document.createElement('p');
            successMessage.className = 'text-sm mt-1';
            successMessage.style.color = 'green';
            successMessage.innerText = "Mot de passe valide.";
            messageContainer.appendChild(successMessage);
        }

        errors.forEach(error => {
            let p = document.createElement('p');
            p.className = 'text-sm mt-1';
            p.style.color = 'red';
            p.innerText = error;
            messageContainer.appendChild(p);
        });

        errors2.forEach(error => {
            let p = document.createElement('p');
            p.className = 'text-sm mt-1';
            p.style.color = 'red';
            p.innerText = error;
            messageContainer2.appendChild(p);
        });
    } else {
        document.getElementById('password').style.borderColor = '';
        document.getElementById('password2').style.borderColor = '';
        isPasswordValid = false;
    }

    updateValidationButton();
}

// Fonction pour vérifier si l'identifiant est déjà pris
let timeout = null;

function checkPseudo() {
    console.log('checkPseudo()');

    let pseudo = document.getElementById('identifiant').value;
    let message = document.getElementById('pseudo-message');
    let identifiantInput = document.getElementById('identifiant');

    clearTimeout(timeout);

    if (pseudo.length < 3) {
        message.innerText = "L'identifiant doit avoir au moins 3 caractères.";
        message.style.color = "red";
        identifiantInput.style.borderColor = 'red';
        isPseudoValid = false;
        updateValidationButton();
        return;
    }

    timeout = setTimeout(() => {
        fetch("http://localhost:8181/identification/check_identifiant.php", {
            method: "POST", 
            body: new URLSearchParams({ identifiant: pseudo }),
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                message.innerText = "❌ identifiant déjà pris.";
                message.style.color = "red";
                identifiantInput.style.borderColor = 'red';
                isPseudoValid = false;
            } else {
                message.innerText = "✅ identifiant disponible.";
                message.style.color = "green";
                identifiantInput.style.borderColor = 'green';
                isPseudoValid = true;
            }
            updateValidationButton();
        })
        .catch(error => {
            console.error("Erreur :", error);
            isPseudoValid = false;
            updateValidationButton();
        });
    }, 500);
}

// 👇 Appel des fonctions au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        checkPassword();
        checkPseudo();
    }, 100);
});
