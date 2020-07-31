/* global referrer_code */
import Share from "./share.js";

function initShare() {
	const options = {
		referrer: referrer_code
	};

	const el = document.querySelector("[data-ignico-share]");
	const share = new Share(el, options);

	share.run();
}

document.addEventListener("DOMContentLoaded", initShare);
