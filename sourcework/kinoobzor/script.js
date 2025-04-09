window.onscroll = function() {
    showFooterOnScroll();
};
window.onload = function() {
    showFooterOnScroll();
};
function showFooterOnScroll() {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        document.getElementById('footer').classList.remove('footer-hidden');
        document.getElementById('footer').classList.add('footer-visible');
    } else {
        document.getElementById('footer').classList.remove('footer-visible');
        document.getElementById('footer').classList.add('footer-hidden');
    }
}

function toggleComments(reviewId) {
    const comments = document.getElementById('comments-' + reviewId);
    comments.style.display = comments.style.display === 'none' ? 'block' : 'none';
    
    if(comments.style.display === 'block') {
        comments.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function sortReviews() {
    const sortValue = document.getElementById('sort').value;
    const reviewList = document.getElementById('review-list');
    const reviews = Array.from(reviewList.children);

    reviews.sort((a, b) => {
        // Получаем рейтинги (новый надежный способ)
        const ratingTextA = a.querySelector('p:nth-of-type(1)').textContent;
        const ratingA = parseInt(ratingTextA.match(/\d+/)[0]);
        
        const ratingTextB = b.querySelector('p:nth-of-type(1)').textContent;
        const ratingB = parseInt(ratingTextB.match(/\d+/)[0]);
        
        // Получаем даты
        const dateTextA = a.querySelector('small').textContent.trim();
        const dateTextB = b.querySelector('small').textContent.trim();
        const dateA = new Date(dateTextA);
        const dateB = new Date(dateTextB);

        switch(sortValue) {
            case 'newest': return dateB - dateA;
            case 'oldest': return dateA - dateB;
            case 'highest': return ratingB - ratingA;
            case 'lowest': return ratingA - ratingB;
            default: return 0;
        }
    });

    // Очищаем и добавляем отсортированные отзывы
    reviewList.innerHTML = '';
    reviews.forEach(review => reviewList.appendChild(review));
}

function rateComment(commentId, rating) {
    fetch('rate_comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `comment_id=${commentId}&rating=${rating}`
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById(`rating-${commentId}`).innerText = data.totalRating;
    })
    .catch(error => console.error('Error:', error));
}

document.querySelectorAll('.save-role-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const currentUserId = this.dataset.currentUser;
        const roleId = this.previousElementSibling.value;

        fetch('update_role.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                user_id: userId, 
                role_id: roleId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Если изменили свою роль - обновляем страницу
                if (userId === currentUserId) {
                    window.location.reload();
                } else {
                    showNotification('Роль успешно обновлена');
                }
            } else {
                showNotification('Ошибка: ' + data.error, 'error');
            }
        })
        .catch(error => {
            showNotification('Ошибка сети', 'error');
        });
    });
});