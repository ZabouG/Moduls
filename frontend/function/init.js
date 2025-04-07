// Définir les valeurs pour test
localStorage.setItem('authToken', 'test'); 
localStorage.setItem('user', 'test'); 

const token = localStorage.getItem('authToken') || '';
const userId = localStorage.getItem('user') || '';

verifyToken(token, userId);

async function verifyToken(token, userId) {
    if (token && userId) {
        try {
            const response = await fetch('http://localhost:8181/verify-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ idUser: userId })
            });

            const data = await response.json();

            if (data.success) {
                console.log('✅ Token is valid:', data.message);
                document.body.innerHTML += `<p style="color:green;">${data.message}</p>`;
            } else {
                console.error('❌ Invalid token:', data.message);
                document.body.innerHTML += `<p style="color:red;">${data.message}</p>`;
                localStorage.removeItem('authToken');
                localStorage.removeItem('user');
                window.location = '/identification/form_connection.html';
            }

        } catch (error) {
            console.error('💥 Error verifying token:', error);
            document.body.innerHTML += `<p style="color:red;">Erreur de vérification.</p>`;
            localStorage.removeItem('authToken');
            localStorage.removeItem('user');
            window.location = '/identification/form_connection.html';
        }
    } else {
        console.warn('⚠️ Aucun token trouvé');
        document.body.innerHTML += `<p style="color:orange;">Aucun token trouvé dans localStorage.</p>`;
        window.location = '/identification/form_connection.html';
    }
}
