document.addEventListener("DOMContentLoaded", () => {
	const chatContainer = document.getElementById('chat-container');
	const chatBox = document.getElementById('chat-box');
	const chatBtn = document.getElementById('chat-btn');
	const sendBtn = document.getElementById('send-btn');
	const inputMessage = document.getElementById('input-message');
	const messages = document.getElementById('messages');
	
	// Function to show the welcome message if no conversation has started yet
	function showWelcomeMessage() {
		if (messages.children.length === 0) { // Check if no messages exist yet
			const welcomeMessageDiv = document.createElement('div');
			welcomeMessageDiv.classList.add('bot-message');
			welcomeMessageDiv.textContent = "Hi, saya Kirana akan memandu Kamu menelusuri website ini. Sampaikan pertanyaanmu.";
			messages.appendChild(welcomeMessageDiv);
			messages.scrollTop = messages.scrollHeight; // Scroll to the latest message
		}
	}

	// Show the welcome message if no conversation exists when the page is loaded
	showWelcomeMessage();

	// Toggle chat box visibility
	chatBtn.addEventListener('click', () => {
		chatBox.style.display = chatBox.style.display === 'none' || chatBox.style.display === '' ? 'flex' : 'none';

	});

	sendBtn.addEventListener('click', () => {
		const userMessage = inputMessage.value.trim();
		if (userMessage) {
			const userMessageDiv = document.createElement('div');
			userMessageDiv.classList.add('user-message');
			userMessageDiv.textContent = userMessage;
			messages.appendChild(userMessageDiv);
			inputMessage.value = '';
	
			// Add typing indicator
			const typingIndicator = document.createElement('div');
			typingIndicator.classList.add('typing-indicator');
			typingIndicator.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span>`;
			messages.appendChild(typingIndicator);
			messages.scrollTop = messages.scrollHeight;
	
			// AJAX request using WordPress pattern
			jQuery.ajax({
				url: ZaraData.ajax_url,
				type: 'POST',
				data: {
					action: 'process_message',
					security: ZaraData.nonce,
					message: userMessage
				}
			})
			.then(response => {
				typingIndicator.remove();
	
				if (response.success) {
					const botMessageDiv = document.createElement('div');
					botMessageDiv.classList.add('bot-message');
					
					// Handle API response structure
					const responseData = response.data;
					if (responseData && responseData.text) {
						botMessageDiv.textContent = responseData.text;
					} else {
						botMessageDiv.textContent = "Menjawab pertanyaan ini, Kamu bisa menghubungi pengelola Prodi melalui email official yang tersedia.";
					}
	
					messages.appendChild(botMessageDiv);
	
					// Add source documents if available
					if (responseData.sourceDocuments && responseData.sourceDocuments.length > 0) {
						responseData.sourceDocuments.forEach(url => {
							const sourceButton = document.createElement('a');
							sourceButton.classList.add('source-button');
							sourceButton.href = url;
							sourceButton.target = "_blank";
							sourceButton.textContent = `Informasi terkait`;
							messages.appendChild(sourceButton);
						});
					}
				} else {
					const errorDiv = document.createElement('div');
					errorDiv.classList.add('bot-message', 'error');
					errorDiv.textContent = response.data.message;
					messages.appendChild(errorDiv);
				}
				
				messages.scrollTop = messages.scrollHeight;
			})
			.catch(error => {
				console.error("Error:", error);
				typingIndicator.remove();
				
				const errorDiv = document.createElement('div');
				errorDiv.classList.add('bot-message', 'error');
				errorDiv.textContent = "Terjadi kesalahan dalam memproses permintaan Anda.";
				messages.appendChild(errorDiv);
				messages.scrollTop = messages.scrollHeight;
			});
		}
	});
});