document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('robot-background');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width, height;

    const mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    
    const robot = {
        x: -150,
        y: window.innerHeight / 2,
        targetX: window.innerWidth / 2,
        targetY: window.innerHeight / 2,
        angle: 0,
        bobbingAngle: 0,
        showWelcome: false,
        finalPositionReached: false
    };

    const easeFactor = 0.05;
    const trail = [];
    const trailLength = 500;

    function init2DRobot() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    }

    window.addEventListener('mousemove', (e) => {
        if (!robot.finalPositionReached) {
             robot.targetX = e.clientX;
             robot.targetY = e.clientY;
        }
    });
    
    function drawWelcomeBubble(x, y) {
        ctx.font = "bold 16px Inter";
        // const text = "Welcome!";
        const textMetrics = ctx.measureText(text);
        
        const bubbleWidth = textMetrics.width + 40;
        const bubbleHeight = 50;
        const bubbleX = x + 10;
        const bubbleY = y - 100;

        ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
        ctx.strokeStyle = 'rgba(251, 146, 60, 0.7)';
        ctx.lineWidth = 2;
        
        ctx.beginPath();
        ctx.roundRect(bubbleX, bubbleY, bubbleWidth, bubbleHeight, 15);
        ctx.fill();
        ctx.stroke();
        
        ctx.beginPath();
        ctx.moveTo(bubbleX + 20, bubbleY + bubbleHeight);
        ctx.lineTo(bubbleX + 30, bubbleY + bubbleHeight + 15);
        ctx.lineTo(bubbleX + 40, bubbleY + bubbleHeight);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();

        ctx.fillStyle = '#000000';
        ctx.fillText(text, bubbleX + 20, bubbleY + 30);
    }

    function drawRobotFace(x, y, angle, bobbingOffset) {
        ctx.save();
        ctx.translate(x, y + bobbingOffset);
        ctx.rotate(angle);
        
        const scale = 0.0001;
        ctx.scale(scale, scale);

        const bodyGradient = ctx.createLinearGradient(0, -50, 0, 50);
        bodyGradient.addColorStop(0, '#ffffff');
        bodyGradient.addColorStop(1, '#d1d5db');

        ctx.fillStyle = bodyGradient;
        ctx.beginPath();
        ctx.roundRect(-45, -45, 90, 90, 25);
        ctx.fill();

        ctx.fillStyle = '#1f2937';
        ctx.beginPath();
        ctx.roundRect(-40, -40, 80, 80, 20);
        ctx.fill();

        const pulse = Math.abs(Math.sin(robot.bobbingAngle * 0.5));
        ctx.shadowBlur = 30 * pulse + 15;
        ctx.shadowColor = '#fb923c';
        
        ctx.strokeStyle = `rgba(251, 146, 60, ${pulse * 0.6 + 0.4})`;
        ctx.lineWidth = 4;
        
        ctx.beginPath();
        ctx.arc(-20, -5, 8, 0, Math.PI * 2);
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(20, -5, 8, 0, Math.PI * 2);
        ctx.stroke();
        
        ctx.beginPath();
        ctx.arc(0, 10, 15, 0.2 * Math.PI, 0.8 * Math.PI);
        ctx.stroke();

        ctx.shadowBlur = 0;

        ctx.restore();
    }

    function update() {
        trail.push({ x: robot.x, y: robot.y });
        if (trail.length > trailLength) {
            trail.shift();
        }

        const dx = robot.targetX - robot.x;
        const dy = robot.targetY - robot.y;
        
        robot.x += dx * easeFactor;
        robot.y += dy * easeFactor;

        robot.angle = (dx * 0.01);
        robot.bobbingAngle += 0.08;
    }

    function animate2DRobot() {
        requestAnimationFrame(animate2DRobot);
        ctx.clearRect(0, 0, width, height);
        
        for (let i = 0; i < trail.length; i++) {
            const pos = trail[i];
            const alpha = i / trail.length;
            ctx.beginPath();
            ctx.arc(pos.x, pos.y, i / 2, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(251, 146, 60, ${alpha * 0.1})`;
            ctx.fill();
        }

        update();
        const bobbingOffset = Math.sin(robot.bobbingAngle) * 10;
        drawRobotFace(robot.x, robot.y, robot.angle, bobbingOffset);

        if (robot.showWelcome) {
            drawWelcomeBubble(robot.x, robot.y);
        }
    }

    window.addEventListener('resize', init2DRobot);
    
    init2DRobot();
    animate2DRobot();
    
    setTimeout(() => {
        robot.targetX = width / 2;
        robot.targetY = height / 3;
        
        setTimeout(() => {
            robot.showWelcome = true;
            robot.finalPositionReached = true;
            setTimeout(() => {
                robot.showWelcome = false;
                robot.finalPositionReached = false;
            }, 4000);
        }, 2500);
    }, 500);
});
