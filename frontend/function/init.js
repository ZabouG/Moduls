const token = localStorage.getItem('token') || '';


verifyToken(token);

async function verifyToken(token) {
    if (token) {
        try {
            const response = await fetch('http://localhost:8181/verifToken.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
            });

            const data = await response.json();

            if (data.success) {
                console.log('✅ Token is valid:', data.message);
            } else {
                console.error('❌ Invalid token:', data.message);
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location = '/identification/form_connection.html';
            }

        } catch (error) {
            console.error('💥 Error verifying token:', error);
            localStorage.removeItem('token'); // ✅ correspond au setItem fait au login
            localStorage.removeItem('user');
            window.location = '/identification/form_connection.html';
        }
    } else {
        console.warn('⚠️ Aucun token trouvé');
        window.location = '/identification/form_connection.html';
    }
}
