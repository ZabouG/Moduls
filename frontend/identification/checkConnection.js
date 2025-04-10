document.getElementById('connectionForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('http://localhost:8181/identification/check-connection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        if (!response.ok) {
            throw new Error('Failed to connect');
        }

        const result = await response.json();!
        console.log('Connection successful:', result);
        // Handle success (e.g., redirect or display a message)
    } catch (error) {
        console.error('Error:', error);
        // Handle error (e.g., display an error message)
    }
});
