<script setup>
</script>

<template>
   <!-- From Uiverse.io by chase2k25 --> 
<div class="page-background w-10">
  <div class="clock-container">
    <div class="clock-face">
      <div class="marker marker-1"><div class="marker-dot"></div></div>
      <div class="marker marker-2"><div class="marker-dot"></div></div>
      <div class="marker marker-3"><div class="marker-dot"></div></div>
      <div class="marker marker-4"><div class="marker-dot"></div></div>
      <div class="marker marker-5"><div class="marker-dot"></div></div>
      <div class="marker marker-6"><div class="marker-dot"></div></div>
      <div class="marker marker-7"><div class="marker-dot"></div></div>
      <div class="marker marker-8"><div class="marker-dot"></div></div>
      <div class="marker marker-9"><div class="marker-dot"></div></div>
      <div class="marker marker-10"><div class="marker-dot"></div></div>
      <div class="marker marker-11"><div class="marker-dot"></div></div>
      <div class="marker marker-12"><div class="marker-dot"></div></div>

      <div class="number number-1"><span class="number-1-text">1</span></div>
      <div class="number number-2"><span class="number-2-text">2</span></div>
      <div class="number number-3"><span class="number-3-text">3</span></div>
      <div class="number number-4"><span class="number-4-text">4</span></div>
      <div class="number number-5"><span class="number-5-text">5</span></div>
      <div class="number number-6"><span class="number-6-text">6</span></div>
      <div class="number number-7"><span class="number-7-text">7</span></div>
      <div class="number number-8"><span class="number-8-text">8</span></div>
      <div class="number number-9"><span class="number-9-text">9</span></div>
      <div class="number number-10"><span class="number-10-text">10</span></div>
      <div class="number number-11"><span class="number-11-text">11</span></div>
      <div class="number number-12"><span class="number-12-text">12</span></div>

      <div class="hand hour-hand"></div>
      <div class="hand minute-hand"></div>
      <div class="hand second-hand"></div>
      <div class="center-pin"></div>
    </div>
  </div>
</div>

</template>

<style>
.page-background {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 1%;
  height: 1%;
  background: #ffffff;
}

.clock-container {
  max-width: 30px;
  max-height: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.clock-face {
  width: 30px;
  height: 30px;
  background-color: #e0e5ec;
  border-radius: 220%;
  box-shadow:
    12px 12px 24px #a3b1c6,
    -12px -12px 24px #ffffff;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
}

.clock-face::before {
  content: "";
  position: absolute;
  width: 9%;
  height: 9%;
  background-color: transparent;
  border-radius: 5%;
  box-shadow:
    inset 8px 8px 16px #a3b1c6,
    inset -8px -8px 16px #ffffff;
}

.marker {
  position: absolute;
  width: 40%;
  height: 40%;
  z-index: 1;
}

.marker-dot {
  position: absolute;
  top: 15px;
  left: 50%;
  transform: translateX(-50%);
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #e0e5ec;
  box-shadow:
    inset 1px 1px 2px #a3b1c6,
    inset -1px -1px 2px #ffffff;
}

.marker-1 {
  transform: rotate(30deg);
}
.marker-2 {
  transform: rotate(60deg);
}
.marker-3 {
  transform: rotate(90deg);
}
.marker-3 .marker-dot {
  width: 8px;
  height: 8px;
}
.marker-4 {
  transform: rotate(120deg);
}
.marker-5 {
  transform: rotate(150deg);
}
.marker-6 {
  transform: rotate(180deg);
}
.marker-6 .marker-dot {
  width: 8px;
  height: 8px;
}
.marker-7 {
  transform: rotate(210deg);
}
.marker-8 {
  transform: rotate(240deg);
}
.marker-9 {
  transform: rotate(270deg);
}
.marker-9 .marker-dot {
  width: 8px;
  height: 8px;
}
.marker-10 {
  transform: rotate(300deg);
}
.marker-11 {
  transform: rotate(330deg);
}
.marker-12 {
  transform: rotate(360deg);
}
.marker-12 .marker-dot {
  width: 8px;
  height: 8px;
}

.number {
  position: absolute;
  width: 1px;
  height: 1px;
  text-align: center;
  font-size: 0px;
  font-weight: 0;
  color: transparent;
  text-shadow:
    1px 1px 1px #ffffff,
    -1px -1px 1px #a3b1c6;
  z-index: 2;
}

.number-1 {
  transform: rotate(30deg);
}
.number-1-text {
  display: inline-block;
  transform: translateY(35px) rotate(-30deg);
}
.number-2 {
  transform: rotate(60deg);
}
.number-2-text {
  display: inline-block;
  transform: translateY(35px) rotate(-60deg);
}
.number-3 {
  transform: rotate(90deg);
}
.number-3-text {
  display: inline-block;
  transform: translateY(35px) rotate(-90deg);
}
.number-4 {
  transform: rotate(120deg);
}
.number-4-text {
  display: inline-block;
  transform: translateY(35px) rotate(-120deg);
}
.number-5 {
  transform: rotate(150deg);
}
.number-5-text {
  display: inline-block;
  transform: translateY(35px) rotate(-150deg);
}
.number-6 {
  transform: rotate(180deg);
}
.number-6-text {
  display: inline-block;
  transform: translateY(35px) rotate(-180deg);
}
.number-7 {
  transform: rotate(210deg);
}
.number-7-text {
  display: inline-block;
  transform: translateY(35px) rotate(-210deg);
}
.number-8 {
  transform: rotate(240deg);
}
.number-8-text {
  display: inline-block;
  transform: translateY(35px) rotate(-240deg);
}
.number-9 {
  transform: rotate(270deg);
}
.number-9-text {
  display: inline-block;
  transform: translateY(35px) rotate(-270deg);
}
.number-10 {
  transform: rotate(300deg);
}
.number-10-text {
  display: inline-block;
  transform: translateY(35px) rotate(-300deg);
}
.number-11 {
  transform: rotate(330deg);
}
.number-11-text {
  display: inline-block;
  transform: translateY(35px) rotate(-330deg);
}
.number-12 {
  transform: rotate(360deg);
}
.number-12-text {
  display: inline-block;
  transform: translateY(35px) rotate(-360deg);
}

.hand {
  position: absolute;
  bottom: 50%;
  left: 50%;
  transform-origin: bottom center;
  border-radius: 10px 10px 0 0;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.15);
}

.hour-hand {
  width: 4px;
  height: 20px;
  background: #4a5463;
  transform: translateX(-50%) rotate(105.5deg);
  animation: rotate-hours 43200s linear infinite;
  animation-play-state: running;
  z-index: 5;
}

.minute-hand {
  width: 6px;
  height: 30px;
  background: #7a8a9e;
  transform: translateX(-50%) rotate(186deg);
  animation: rotate-minutes 3600s linear infinite;
  animation-play-state: running;
  z-index: 6;
}

.second-hand {
  width: 3px;
  height: 30px;
  background: #e65e5e;
  transform: translateX(-50%) rotate(0deg);
  animation: rotate-seconds 60s steps(60, end) infinite;
  animation-play-state: running;
  z-index: 7;
}

.center-pin {
  width: 20px;
  height: 20px;
  background: #e0e5ec;
  border-radius: 50%;
  position: absolute;
  z-index: 10;
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
}

.center-pin::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 6px;
  height: 6px;
  background: #4a5463;
  border-radius: 50%;
}

@keyframes rotate-seconds {
  from {
    transform: translateX(-50%) rotate(0deg);
  }
  to {
    transform: translateX(-50%) rotate(360deg);
  }
}

@keyframes rotate-minutes {
  from {
    transform: translateX(-50%) rotate(186deg);
  }
  to {
    transform: translateX(-50%) rotate(546deg);
  }
}

@keyframes rotate-hours {
  from {
    transform: translateX(-50%) rotate(105.5deg);
  }
  to {
    transform: translateX(-50%) rotate(465.5deg);
  }
}


</style>
