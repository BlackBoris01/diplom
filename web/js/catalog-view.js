$(document).ready(function() {
    // Обработка формы отзыва
    $('.review-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $.ajax({
            url: '/catalog/catalog/review',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    showMessage('Спасибо за ваш отзыв!', 'success');
                    form[0].reset();
                    location.reload();
                } else {
                    showMessage(response.message || 'Произошла ошибка при сохранении отзыва', 'error');
                }
            },
            error: function() {
                showMessage('Произошла ошибка при отправке отзыва', 'error');
            }
        });
    });

    // Обработка кнопки "Показать еще"
    $('.show-more-link').on('click', function(e) {
        e.preventDefault();
        var link = $(this);
        var reviewRole = link.data('role');
        var offset = parseInt(link.data('offset'));
        var limit = parseInt(link.data('limit'));
        
        $.get('/catalog/catalog/load-more-reviews', {
            bookId: window.bookId,
            offset: offset,
            limit: limit,
            role: reviewRole
        })
        .done(function(response) {
            if (response.success) {
                $(response.html).insertBefore(link.parent());
                
                var newOffset = offset + limit;
                var newLimit = offset < 8 ? 5 : 10;
                
                if (response.hasMore) {
                    link.data('offset', newOffset);
                    link.data('limit', newLimit);
                } else {
                    link.parent().remove();
                }
            } else {
                showMessage('Произошла ошибка при загрузке отзывов', 'error');
            }
        })
        .fail(function() {
            showMessage('Произошла ошибка при загрузке отзывов', 'error');
        });
    });

    // Открытие/закрытие формы отзыва
    $('.review-toggle').on('click', function(e) {
        e.preventDefault();
        $('#review-form').slideToggle();
    });

    // Работа со звёздами рейтинга
    const stars = document.querySelectorAll('.catalog-view-star');
    const ratingValue = document.querySelector('.catalog-view-rating-value');
    let selectedRating = parseInt(ratingValue ? ratingValue.textContent : 7) || 7;

    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => {
            updateStars(index + 1);
        });
        star.addEventListener('mouseout', () => {
            updateStars(selectedRating);
        });
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            updateStars(selectedRating);
            submitRating(selectedRating);
        });
    });

    function updateStars(count) {
        stars.forEach((star, index) => {
            star.classList.toggle('active', index < count);
        });
        if (ratingValue) ratingValue.textContent = count + '/10';
    }

    function submitRating(rating) {
        $.ajax({
            url: '/catalog/catalog/rate',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                bookId: window.bookId,
                rating: rating
            }),
            headers: {
                'X-CSRF-Token': yii.getCsrfToken()
            },
            success: function(data) {
                if (data.success) {
                    showMessage('Спасибо за вашу оценку!', 'success');
                    // Можно обновить статистику рейтинга, если нужно
                } else {
                    showMessage('Произошла ошибка при сохранении оценки', 'error');
                }
            },
            error: function() {
                showMessage('Произошла ошибка при отправке оценки', 'error');
            }
        });
    }

    // Функция для показа сообщений
    function showMessage(text, type) {
        var messageDiv = $('<div>')
            .addClass('alert alert-' + (type === 'success' ? 'success' : 'danger'))
            .css({
                position: 'fixed',
                top: '20px',
                right: '20px',
                zIndex: 1000
            })
            .text(text);
        
        $('body').append(messageDiv);
        
        setTimeout(function() {
            messageDiv.remove();
        }, 3000);
    }
}); 