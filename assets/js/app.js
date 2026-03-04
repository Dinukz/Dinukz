document.querySelectorAll('.card, .hero').forEach((el, i) => {
  el.animate([
    { transform: 'translateY(12px)', opacity: 0 },
    { transform: 'translateY(0)', opacity: 1 }
  ], { duration: 500 + i * 80, easing: 'ease-out', fill: 'forwards' });
});
