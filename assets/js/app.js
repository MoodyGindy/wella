const baseUrl = (window.APP_BASE_URL || '/').replace(/\/?$/, '/');

const buildUrl = (path) => {
    const normalizedPath = String(path || '').replace(/^\//, '');
    return baseUrl + normalizedPath;
};

document.addEventListener('DOMContentLoaded', () => {
    bindCartButtons();
    bindFavoriteButtons();
    setupStickyHeader();
});

function bindCartButtons() {
    document.querySelectorAll('[data-action="add-to-cart"]').forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const productId = button.dataset.productId;
            const quantity = button.dataset.quantity || 1;

            const response = await postJSON(buildUrl('api/cart.php'), {
                action: 'add',
                product_id: Number(productId),
                quantity: Number(quantity),
            });

            showAlert(response?.message || 'Added to cart.');
        });
    });

    document.querySelectorAll('[data-action="remove-from-cart"]').forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const productId = button.dataset.productId;

            const response = await postJSON(buildUrl('api/cart.php'), {
                action: 'remove',
                product_id: Number(productId),
            });

            if (response?.redirect) {
                window.location.reload();
            }
        });
    });

    const cartUpdateForms = document.querySelectorAll('form[data-role="update-cart"]');
    cartUpdateForms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const productId = form.dataset.productId;
            const quantity = form.querySelector('input[name="quantity"]').value;

            await postJSON(buildUrl('api/cart.php'), {
                action: 'update',
                product_id: Number(productId),
                quantity: Number(quantity),
            });

            window.location.reload();
        });
    });
}

function bindFavoriteButtons() {
    document.querySelectorAll('[data-action="toggle-favorite"]').forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const productId = button.dataset.productId;
            const isFavorite = button.dataset.isFavorite === '1';

            const response = await postJSON(buildUrl('api/favorites.php'), {
                action: isFavorite ? 'remove' : 'add',
                product_id: Number(productId),
            });

            if (response?.status === 'ok') {
                const nowFavorite = !isFavorite;
                const labelText = nowFavorite ? 'Remove favorite' : 'Add to favorites';

                button.dataset.isFavorite = nowFavorite ? '1' : '0';
                button.classList.toggle('is-active', nowFavorite);
                button.setAttribute('aria-label', labelText);

                const srOnly = button.querySelector('.sr-only');
                if (srOnly) {
                    srOnly.textContent = labelText;
                }
            }

            showAlert(response?.message || 'Favorite updated.');
        });
    });
}

function setupStickyHeader() {
    const header = document.querySelector('.site-header');

    if (!header) {
        return;
    }

    const updateState = () => {
        const offset = window.scrollY || window.pageYOffset;
        const hasHero = document.body.classList.contains('has-hero');
        header.classList.toggle('is-scrolled', !hasHero || offset > 24);
    };

    const updateHeaderOffset = () => {
        const height = header.offsetHeight || 0;
        document.documentElement.style.setProperty('--header-offset', `${height}px`);
    };

    updateHeaderOffset();
    window.addEventListener('resize', updateHeaderOffset, { passive: true });

    updateState();
    window.addEventListener('scroll', updateState, { passive: true });
}

async function postJSON(url, payload) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        return response.json();
    } catch (error) {
        console.error(error);
        showAlert('Something went wrong. Please try again.', 'error');
        return null;
    }
}

function showAlert(message, type = 'success') {
    let alert = document.querySelector('.js-alert');

    if (!alert) {
        alert = document.createElement('div');
        alert.className = `alert alert-${type} js-alert`;
        document.body.prepend(alert);
    }

    alert.textContent = message;
    alert.className = `alert alert-${type} js-alert`;

    setTimeout(() => {
        alert?.remove();
    }, 3000);
}


