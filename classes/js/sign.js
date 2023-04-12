//Define variables
const form = document.querySelector('.sign-div');
const canvas = document.querySelector('.sign-canvas');
const clearBtn = document.querySelector('.clear-btn');
const ctx = canvas.getContext('2d');
const subBtn = document.querySelector('.submit-btn');
let wriitingMode = false;

//Clear cavnas
const clear = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

//Event for clear btn
clearBtn.addEventListener('click', (event) => {
    event.preventDefault();
    clear();
})

//Get position
const getPosition = (event) => {
    positionY = event.clientY - event.target.getBoundingClientRect().y;
    positionX = event.clientX - event.target.getBoundingClientRect().x;
    return [positionX, positionY];
}

//Event for when pointer moves
const pointerMove = (event) => {
    if (!writingMode) return;
    const [positionX, positionY] = getPosition(event);
    ctx.lineTo(positionX, positionY);
    ctx.stroke();
}

//change writing mode on pointer up
const pointerUp = () => {
    writingMode = false;
}

//change writing mode on pointer down and draw line
const pointerDown = (event) => {
    writingMode = true;
    ctx.beginPath();
    const [positionX, positionY] = getPosition(event);
    ctx.moveTo(positionX, positionY);
}

//Event for submit button
subBtn.addEventListener('click', (event) => {
    const imageURL = canvas.toDataURL();
    const input = document.createElement('input');
    input.type = 'text';
    input.value = imageURL;
    input.name = 'signature';
    input.hidden = true;
    form.appendChild(input);
    clearBtn.disabled = true;
    clearBtn.className = 'btn-secondary mb-2 mr-2 p-2 clear-btn';
    subBtn.disabled = true;
    subBtn.className = 'btn-secondary mb-2 mr-2 p-2 submit-btn';
    canvas.className = 'sign-canvas border disablecan';
    clearPad();
})

const mouseout = () => {
    writingMode = false;
}

ctx.lineWidth = 3;
ctx.lineJoin = ctx.lineCap = 'round';

    
canvas.addEventListener('pointerdown', pointerDown, {passive: true});
canvas.addEventListener('pointerup', pointerUp, {passive: true});
canvas.addEventListener('pointermove', pointerMove, {passive: true});
canvas.addEventListener('mouseout', mouseout, {passive: true});
