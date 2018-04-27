(function () {
    // landing page variables
    const left = document.querySelector('.left');
    const right = document.querySelector('.right');
    const container = document.querySelector('.container');

    //left mouse enter event
    left.addEventListener('mouseenter', () => {
        container.classList.add('hover-left');
    });// end left mouse enter

    // left mouse leave event
    left.addEventListener('mouseleave', () => {
        container.classList.remove('hover-left');
    });// end left mouse leave

    // right mouse enter event
    right.addEventListener('mouseenter', () => {
        container.classList.add('hover-right');
    });// end right mouse enter

    // right mouse leave event
    right.addEventListener('mouseleave', () => {
        container.classList.remove('hover-right');
    });// end right mouse leave
}());
