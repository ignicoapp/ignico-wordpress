import ClipboardJS from "clipboard/dist/clipboard.min";

const defaults = {
	link: null,
	referrer: null,
	subject: "",
	message: "{{link}}",
	tooltip: {
		class: "ignico-tooltipped",
		classPosition: "ignico-tooltipped-n",
		duration: 2000
	},
	selectors: {
		btnSocialsEmail: "[data-ignico-socials-email]",
		btnSocialsLink: "[data-ignico-socials-link]"
	}
};

class Share {
	constructor(el, options) {
		this.container = el;
		this.options = Object.assign({}, defaults, options);
	}

	initialize() {
		this.initializeElements();
		this.initializeLinks();
		this.initializeMessage();
		this.initializeClipboard();
	}

	initializeElements() {
		this.btnSocialsEmail = this.container.querySelector(this.options.selectors.btnSocialsEmail);
		this.btnSocialsLink = this.container.querySelector(this.options.selectors.btnSocialsLink);
	}

	initializeLinks() {
		this.link = this.options.link === null ? getBaseLink() : this.options.link;
		this.referralLink = `${this.link}?__igrc=${this.options.referrer}`;
	}

	initializeMessage() {
		this.subject = this.options.subject;
		this.message = this.options.message.replace("{{link}}", this.referralLink);
	}

	initializeClipboard() {
		const btnSocialsLink = this.btnSocialsLink;
		const referralLink = this.referralLink;

		/**
		 * ClipboardJS is not aware of exising iframes, by default it is searching
		 * for selectors in document.body. If we are passing HTMLElement directly
		 * Chrome can not recognize element `trigger instanceof HTMLElement` is
		 * returing false. Https://stackoverflow.com/a/26251098.
		 *
		 * We have to overwrite `listenClick` method to create own listener.
		 */
		ClipboardJS.prototype.listenClick = function() {
			btnSocialsLink.addEventListener("click", e => this.onClick(e));
		};

		// First argument does not matter since we are creating own event listener.
		const clipboard = new ClipboardJS("fake", {
			text: function() {
				return referralLink;
			}
		});

		clipboard.on("success", success.bind(this));

		function success(e) {
			addTooltipClasses.call(this, e.trigger);
			setTimeout(timeout.bind(this, e.trigger), this.options.tooltip.duration);
		}

		function timeout(el) {
			removeTooltipClasses.call(this, el);
		}

		function addTooltipClasses(el) {
			el.classList.add(this.options.tooltip.class);
			el.classList.add(this.options.tooltip.classPosition);
		}

		function removeTooltipClasses(el) {
			el.classList.remove(this.options.tooltip.class);
			el.classList.remove(this.options.tooltip.classPosition);
		}
	}

	bindEvents() {
		if (this.btnSocialsEmail) {
			this.btnSocialsEmail.addEventListener("click", this.shareThroughEmail.bind(this));
		}
	}

	shareThroughEmail(e) {
		const message = encodeURIComponent(this.message);
		const subject = encodeURIComponent(this.subject);

		window.location.href = `mailto:?subject=${subject}&body=${message}`;
	}

	run() {
		this.initialize();
		this.bindEvents();
	}
}

function getBaseLink() {
	return `${window.location.protocol}//${window.location.host}`;
}

export default Share;
