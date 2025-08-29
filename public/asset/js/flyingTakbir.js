// public/js/flyingTakbir.js

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(e) {
        createTakbir(e.clientX, e.clientY);
    });
});

function createTakbir(x, y) {
    const takbir = document.createElement('div');
    takbir.innerText = 'الله أكبر';
    takbir.classList.add('flying-takbir');
    
    // Position the element at the click coordinates
    takbir.style.left = `${x}px`;
    takbir.style.top = `${y}px`;

    document.body.appendChild(takbir);

    // Animation properties
    const animation = takbir.animate([
        // keyframes
        { transform: 'translate(-50%, -50%) scale(1)', opacity: 1 },
        { transform: 'translate(-50%, -250%) scale(1.5)', opacity: 0 }
    ], {
        // timing options
        duration: 2000,
        easing: 'ease-out'
    });

    // Remove the element after the animation is finished
    animation.onfinish = () => {
        takbir.remove();
    };
}
