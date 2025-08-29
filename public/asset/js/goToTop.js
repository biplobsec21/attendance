document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.main-content');
    const goToTopBtn = document.getElementById("go-to-top");

    if (mainContent && goToTopBtn) {
        mainContent.addEventListener('scroll', () => {
            if (mainContent.scrollTop > 300) {
                goToTopBtn.style.display = "block";
                goToTopBtn.style.opacity = "1";
            } else {
                goToTopBtn.style.opacity = "0";
                setTimeout(() => {
                    if (mainContent.scrollTop <= 300) {
                        goToTopBtn.style.display = "none";
                    }
                }, 300);
            }
        });

        goToTopBtn.addEventListener('click', () => {
            mainContent.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
