<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Spannr Ventures</title>
    @vite('resources/sass/matter-logos.scss')
</head>
<body>
@if (!$hideHeader)
    <div class="welcome">
        <h1>Spannr Ventures</h1>
        <p>For accredited investors looking to back the next generation of private companies that are transforming human healthspans and lifespans</p>
        <p><a href="{{ route('public.content.ventures') }}">LEARN MORE</a></p>
    </div>
@endif

<div class="matter-container">
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/paradromics.png') }}" alt="Pandromics">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/aria.png') }}" alt="Aria Pharmaceuticals">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/mind-portal.png') }}" alt="Mind Portal">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/blackrock.png') }}" alt="Blackrock Neurotech">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/bright-minds.png') }}" alt="Bright Minds">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/character.png') }}" alt="Character">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/gerostate.png') }}" alt="Gerostate Alpha">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/juvenescence.png') }}" alt="Juvenescence">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/maya.png') }}" alt="Maya Health">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/sensorium.png') }}" alt="Sensorium">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/tripp.png') }}" alt="Tripp">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/vessel.png') }}" alt="Vessel">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/rain.png') }}" alt="Rain">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/rejuveron.png') }}" alt="Rejuveron">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/colossal.png') }}" alt="Colossal">
    </div>
    <div class="matter-logo">
        <img src="{{ Vite::asset('resources/images/ventures/circle-logos/apollo.png') }}" alt="Apollo">
    </div>
</div>
<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.18.0/matter.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/matter-attractors@0.1.6/build/matter-attractors.min.js'></script>
<script src='https://unpkg.co/gsap@3/dist/gsap.min.js'></script>
<script>
    const logoContainer = document.querySelector(".matter-container");
    const logoContent = logoContainer.querySelectorAll(".matter-logo");

    let containerWidth = logoContainer.offsetWidth;
    let containerMidX = containerWidth / 2;
    let containerHeight = logoContainer.offsetHeight;
    let containerMidY = containerHeight / 2;

    const shiftAmount = 10;

    const centerPoint = {
        x: containerMidX,
        y: containerMidY,
    };

    const attractionPower = 6e-6;

    const fullLogoSize = logoContent[0].offsetWidth;
    const logoSizeMultiplier = 0.7;
    const defaultLogoSize = fullLogoSize * logoSizeMultiplier;

    const physicsSizeMultiplier = 1.1;
    const fullPhysicsRadius = fullLogoSize * physicsSizeMultiplier * 0.5;
    const defaultPhysicsRadius = defaultLogoSize * physicsSizeMultiplier * 0.5;


    /************************/
    /* Create physics world */
    /************************/
    Matter.use(MatterAttractors);
    const Engine = Matter.Engine,
        World = Matter.World,
        Bodies = Matter.Bodies,
        MouseConstraint = Matter.MouseConstraint;

    const engine = Engine.create();
    engine.world.gravity.scale = 0;

    const attractiveBody = Bodies.circle(
        centerPoint.x,
        centerPoint.y,
        0,
        {
            mass: 1,
            plugin: {
                attractors: [
                    function (bodyA, bodyB) {
                        const diffX = (bodyA.position.x - bodyB.position.x);
                        const diffY = (bodyA.position.y - bodyB.position.y);

                        return {
                            x: diffX * attractionPower,
                            y: diffY * attractionPower
                        };
                    }
                ]
            }
        }
    );
    attractiveBody.collisionFilter = {
        group: -1,
        category: 2,
        mask: 0
    };

    World.add(engine.world, attractiveBody);


    /******************************/
    /* Create logo represenations */
    /******************************/
    const logos = [];
    const logoBodies = [];
    let loadedLogoCount = 0;
    const numLogos = logoContent.length;
    for (let i = 0; i < numLogos; i++) {
        const logoElem = logoContent[i];
        logoElem.draggable = false;
        const logoImg = logoElem.querySelector("img");

        const logo = {
            body: Matter.Bodies.circle(
                Math.random() * containerWidth,
                Math.random() * containerHeight,
                defaultPhysicsRadius
            ),
            scale: logoSizeMultiplier,
            elem: logoElem,
            animationProgress: 0,
            render() {
                const {x, y} = this.body.position;
                this.elem.style.transform = `translate(${x}px, ${y}px) scale(${this.scale})`;
            }
        };
        logo.hoverAnimation = gsap.to(logo, {
            paused: true,
            duration: 0.3,
            animationProgress: 1,
            onUpdate: () => updatePhysicsBody(logo),
        });

        logos.push(logo);
        logoBodies.push(logo.body);

        World.add(engine.world, logo.body);

        if (logoImg.complete) {
            handleImgLoaded();
        } else {
            logoImg.addEventListener("load", handleImgLoaded);
        }
    }

    function updatePhysicsBody(logo) {
        // Set DOM scale based on animation progress
        const progress = logo.animationProgress;
        logo.scale = logoSizeMultiplier + (1 - logoSizeMultiplier) * progress;

        // Set physics scale based on animation progress
        const targetSizeThisTick = defaultPhysicsRadius + (fullPhysicsRadius - defaultPhysicsRadius) * progress;
        const scaleThatNeedsToHappen = targetSizeThisTick / logo.body.circleRadius;
        Matter.Body.scale(logo.body, scaleThatNeedsToHappen, scaleThatNeedsToHappen);
    }

    function handleImgLoaded() {
        loadedLogoCount++;

        if (loadedLogoCount === numLogos) {
            init();
        }
    }


    /*************************/
    /* Pointer functionality */
    /*************************/
    function addHoverEffects() {
        const mouseConstraint = MouseConstraint.create(engine, {
            constraint: {
                // stiffness: 0.2,
                render: {
                    visible: false
                }
            }
        });

        World.add(engine.world, mouseConstraint);

        Matter.Events.on(mouseConstraint, 'mousemove', function (event) {
            const mousePos = event.mouse.position;
            centerPointTranslation(mousePos);
            logoHoverEffect(mousePos);
        });
    }

    function centerPointTranslation(pos) {
        const percentX = (containerMidX - pos.x) / containerMidX;
        const percentY = (containerMidY - pos.y) / containerMidY;

        gsap.to(centerPoint, {
            x: containerMidX + shiftAmount * percentX,
            y: containerMidY + shiftAmount * percentY,
            overwrite: true,
            onUpdate: () => {
                attractiveBody.position.x = centerPoint.x;
                attractiveBody.position.y = centerPoint.y;
            }
        });
    }

    function logoHoverEffect(pos) {
        const foundPhysics = Matter.Query.point(logoBodies, pos);

        let logo;
        if (foundPhysics.length) {
            const body = foundPhysics[0];

            // Find relevant logo
            const index = logoBodies.indexOf(body);
            logo = logos[index];

            // Fire the animation
            logo.hoverAnimation.play();
        }

        logos.forEach((l) => {
            if (logo === l) return;

            l.hoverAnimation.reverse();
        });
    }


    /*****************/
    /* Miscellaneous */
    /*****************/

    new ResizeObserver(() => {
        containerWidth = logoContainer.offsetWidth;
        containerMidX = containerWidth / 2;
        containerHeight = logoContainer.offsetHeight;
        containerMidY = containerHeight / 2;

        centerPointTranslation({x: containerMidX, y: containerMidY});
    }).observe(logoContainer);

    function init() {
        logoContent.forEach((el) => el.classList.add("loaded"));

        render();

        addHoverEffects();
    }

    function render() {
        logos.forEach((circle) => circle.render());
        Matter.Engine.update(engine);
        requestAnimationFrame(render);
    }
</script>
</body>
</html>
