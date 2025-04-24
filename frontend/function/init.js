const token = localStorage.getItem('token') || '';

verifyToken(token).then(valid => {
    if (!valid) return;
    // ici tu peux continuer à afficher la page (ex: charger le contenu sécurisé)
});

async function verifyToken(token) {
    if (!token) {
        console.warn('⚠️ Aucun token trouvé');
        redirectToLogin();
        return false;
    }

    try {
        const response = await fetch('http://localhost:8181/identification/verifToken.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        const text = await response.text();
        console.log("Réponse brute reçue :", text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('❌ Erreur de parsing JSON :', e);
            redirectToLogin();
            return false;
        }

        if (data.success) {
            console.log('✅ Token valide. Utilisateur connecté :', data.user);
            return true;
        } else {
            console.error('❌ Token invalide ou expiré :', data.message);
            redirectToLogin();
            return false;
        }
    } catch (error) {
        console.error('💥 Erreur lors de la vérification du token :', error);
        redirectToLogin();
        return false;
    }
}

function redirectToLogin() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location = '/identification/form_connection.html';
}
