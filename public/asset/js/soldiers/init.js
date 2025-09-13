// public/js/soldiers/init.js
import SoldierProfileManager from "./SoldierProfileManager.js";

window.addEventListener("DOMContentLoaded", () => {
    window.soldierManager = new SoldierProfileManager();
    soldierManager.init();
});
