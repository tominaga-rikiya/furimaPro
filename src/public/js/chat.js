class TransactionChat {
    constructor(config) {
        this.selectedRating = 0;
        this.config = config;
        this.init();
    }

    init() {
        this.initRatingSystem();
        this.initFileUpload();
        this.initMessageForm();
        this.initScrollManagement();
        this.initImageModal();
        this.initEditActions();
        this.checkAutoOpenRating();
        this.initModalClickHandler();
    }

    initRatingSystem() {
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingForm = document.getElementById('rating-form');
        const ratingStarsContainer = document.getElementById('rating-stars');

        ratingStars.forEach(star => {
            star.addEventListener('click', (e) => this.handleStarClick(e));
            star.addEventListener('mouseenter', (e) => this.handleStarHover(e));
        });

        if (ratingStarsContainer) {
            ratingStarsContainer.addEventListener('mouseleave', () => {
                this.updateStars();
            });
        }

        if (ratingForm) {
            ratingForm.addEventListener('submit', (e) => {
                this.handleRatingSubmit(e);
            });
        }
    }

    handleStarClick(e) {
        this.selectedRating = parseInt(e.target.dataset.rating);
        this.updateStars();
        
        const submitBtn = document.getElementById('rating-submit-btn');
        const errorElement = document.getElementById('score-error');
        
        if (submitBtn) submitBtn.disabled = false;
        if (errorElement) errorElement.style.display = 'none';
    }

    handleStarHover(e) {
        const rating = parseInt(e.target.dataset.rating);
        this.highlightStars(rating);
    }

    updateStars() {
        document.querySelectorAll('.rating-star').forEach((star, index) => {
            if (index < this.selectedRating) {
                star.classList.add('active');
                star.style.color = '#ffd700';
            } else {
                star.classList.remove('active');
                star.style.color = '#ddd';
            }
        });
    }

    highlightStars(rating) {
        document.querySelectorAll('.rating-star').forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffd700';
            } else {
                star.style.color = '#ddd';
            }
        });
    }

    resetRatingForm() {
        this.selectedRating = 0;
        document.querySelectorAll('.rating-star').forEach(star => {
            star.classList.remove('active');
        });
        
        const submitBtn = document.getElementById('rating-submit-btn');
        if (submitBtn) submitBtn.disabled = true;
        
        document.querySelectorAll('.rating-error').forEach(error => {
            error.style.display = 'none';
        });
    }

    async handleRatingSubmit(e) {
        e.preventDefault();
        
        if (this.selectedRating === 0) {
            const errorElement = document.getElementById('score-error');
            if (errorElement) {
                errorElement.textContent = '評価を選択してください';
                errorElement.style.display = 'block';
            }
            return;
        }

        const submitBtn = document.getElementById('rating-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = '送信中...';
        }

        try {
            if (this.config.needsCompletion) {
                await this.completeTransaction();
            }
            await this.submitRating();
            
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || '処理中にエラーが発生しました');
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = '送信する';
            }

            const completeBtn = document.querySelector('.complete-transaction-button');
            if (completeBtn) {
                completeBtn.disabled = false;
                completeBtn.textContent = '取引を完了する';
            }
        }
    }

    async completeTransaction() {
        const formData = new FormData();
        formData.append('_token', this.config.csrfToken);
        
        const response = await fetch(this.config.routes.completeTransaction, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || '取引完了に失敗しました');
        }
    }

    async submitRating() {
        const formData = new FormData();
        formData.append('_token', this.config.csrfToken);
        formData.append('score', this.selectedRating);

        const response = await fetch(this.config.routes.storeRating, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success) {
            alert('評価を送信しました');
            window.location.href = '/';
        } else {
            throw new Error(data.message || '評価送信に失敗しました');
        }
    }

    initFileUpload() {
        const fileInput = document.getElementById('target');
        const fileBtn = document.getElementById('file-btn');
        const selectedFileInfo = document.getElementById('selected-file-info');
        
        if (fileInput && fileBtn && selectedFileInfo) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const fileSize = Math.round(this.files[0].size / 1024);
                    
                    fileBtn.classList.add('file-selected');
                    selectedFileInfo.textContent = `選択済み: ${fileName} (${fileSize}KB)`;
                    selectedFileInfo.classList.add('show');
                } else {
                    fileBtn.classList.remove('file-selected');
                    selectedFileInfo.classList.remove('show');
                }
            });
        }
    }

    initMessageForm() {
        const messageForm = document.getElementById('message-form');
        const messageInput = document.querySelector('input[name="content"]');
        const fileInput = document.getElementById('target');
        const fileBtn = document.getElementById('file-btn');
        const selectedFileInfo = document.getElementById('selected-file-info');
        
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                setTimeout(function() {
                    if (messageInput) messageInput.value = '';
                    if (fileInput) {
                        fileInput.value = '';
                        if (fileBtn) fileBtn.classList.remove('file-selected');
                        if (selectedFileInfo) selectedFileInfo.classList.remove('show');
                    }
                    
                    const transactionId = window.location.pathname.split('/').pop();
                    const storageKey = `transaction_message_${transactionId}`;
                    sessionStorage.removeItem(storageKey);
                }, 100);
            });
        }

        if (messageInput) {
            const transactionId = window.location.pathname.split('/').pop();
            const storageKey = `transaction_message_${transactionId}`;
            
            const savedMessage = sessionStorage.getItem(storageKey);
            if (savedMessage) {
                messageInput.value = savedMessage;
            }
            
            messageInput.addEventListener('input', function() {
                const currentValue = this.value.trim();
                if (currentValue) {
                    sessionStorage.setItem(storageKey, currentValue);
                } else {
                    sessionStorage.removeItem(storageKey);
                }
            });
            
            window.addEventListener('beforeunload', function() {
                const currentValue = messageInput.value.trim();
                if (currentValue) {
                    sessionStorage.setItem(storageKey, currentValue);
                } else {
                    sessionStorage.removeItem(storageKey);
                }
            });
        }
    }

    initScrollManagement() {
        const messagesArea = document.querySelector('.messages-area');
        
        if (messagesArea) {
            const savedScrollPosition = sessionStorage.getItem('chatScrollPosition');
            if (savedScrollPosition) {
                setTimeout(function() {
                    messagesArea.scrollTop = parseInt(savedScrollPosition);
                    sessionStorage.removeItem('chatScrollPosition');
                }, 100);
            }

            const urlParams = new URLSearchParams(window.location.search);
            const editingMessageId = urlParams.get('edit');
            if (editingMessageId) {
                setTimeout(function() {
                    const editingMessage = document.getElementById('message-' + editingMessageId);
                    if (editingMessage) {
                        editingMessage.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 200);
            }
        }
    }

    initEditActions() {
        document.querySelectorAll('.btn-edit').forEach(function(editBtn) {
            editBtn.addEventListener('click', function(e) {
                const messagesArea = document.querySelector('.messages-area');
                if (messagesArea) {
                    sessionStorage.setItem('chatScrollPosition', messagesArea.scrollTop);
                }
            });
        });
    }

    initImageModal() {
        window.openImageModal = function(img) {
            const modalImage = document.getElementById('modal-image');
            const imageModal = document.getElementById('image-modal');
            
            if (modalImage && imageModal) {
                modalImage.src = img.src;
                imageModal.style.display = 'block';
            }
        };

        window.closeImageModal = function() {
            const imageModal = document.getElementById('image-modal');
            if (imageModal) {
                imageModal.style.display = 'none';
            }
        };
    }

    openRatingModal() {
        const ratingModal = document.getElementById('rating-modal');
        if (ratingModal) {
            ratingModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    completeTransactionAndShowRating() {
        this.openRatingModal();
        
        const completeBtn = document.querySelector('.complete-transaction-button');
        if (completeBtn) {
            completeBtn.disabled = true;
            completeBtn.textContent = '評価待ち...';
        }
    }

    checkAutoOpenRating() {
        if (this.config.shouldAutoOpenRating) {
            setTimeout(() => {
                this.openRatingModal();
            }, 100);
        }
    }

    initModalClickHandler() {
        const ratingModal = document.getElementById('rating-modal');
        if (ratingModal) {
            ratingModal.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
}

window.completeTransactionAndShowRating = function() {
    if (window.transactionChatInstance) {
        window.transactionChatInstance.completeTransactionAndShowRating();
    }
};

window.openRatingModal = function() {
    if (window.transactionChatInstance) {
        window.transactionChatInstance.openRatingModal();
    }
};