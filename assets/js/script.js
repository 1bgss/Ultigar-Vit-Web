function sendOption(option) {
    const chatBox = document.getElementById('chat-box');
    const userMessage = `<div class="message user">${option}</div>`;
    chatBox.innerHTML += userMessage;

    fetch('chat-bot/process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `message=${encodeURIComponent(option)}`
    })
        .then(response => response.json())
        .then(data => {
            const botMessage = `<div class="message bot">${data.response}</div>`;
            chatBox.innerHTML += botMessage;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(err => console.error('Error:', err));
}

document.getElementById('send').addEventListener('click', function () {
    const messageBox = document.getElementById('message');
    const message = messageBox.value;

    if (message.trim() !== "") {
        sendOption(message);
        messageBox.value = "";
    }
});

function showProductDetails(productId) {
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.image_path) {
                const imagePath = `../uploads/products/${data.image_path}`;
                console.log(imagePath); // Cek path gambar yang dihasilkan
                const modalDetails = document.getElementById('modal-details');
                modalDetails.innerHTML = `
                
                    <h3>${data.name}</h3>
                    <p>${data.description}</p>
                    <a href="${data.shopee_link}" target="_blank" class="buy-button">Beli di Shopee</a>
                `;
                document.getElementById('product-modal').style.display = 'flex';
            } else {
                console.error('Image not found');
                // Optional: Handle case where image is not found
            }
        }).catch(error => {
            console.error('Error fetching product details:', error);
        });
}




function closeModal() {
    document.getElementById('product-modal').style.display = 'none';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById('product-modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}


