// import createScrollSnap from 'scroll-snap'


function safeGetStorage() {

	var storage;
	var fail;
	var uid;
	try {
		uid = new Date;
		(storage = window.localStorage).setItem(uid, uid);
		fail = storage.getItem(uid) != uid;
		storage.removeItem(uid);
		fail && (storage = false);
	} catch (exception) {};
	return storage;
}


window.checkForIntro = function (introUrl) {
	const storage = safeGetStorage();
	if (storage) {
		const currentPalette = storage.getItem('colorpalette');
		if (!currentPalette) {
			window.location = introUrl;
		}
	}
}

export default (options) => ({
	allColorpalettes: options.colorpalettes,
	currentColorpalette: null,
	main_expanded: true,
	expanded_stripe_num: 0,
	expanded_palettechooser: false,
	storage: 'untested',
	openStripe(stripenum, url) {
		this.expanded_stripe_num = stripenum;


		let prerenderLink = document.createElement("link");
		prerenderLink.setAttribute("rel", "prefetch");
		prerenderLink.setAttribute("href", url);
		document.head.appendChild(prerenderLink);

		if (url) {
			setTimeout(() => {
				window.location = url
			}, 1000);
		}

	},
	closeStripe(stripenum) {

		this.expanded_stripe_num = 0;
	},
	togglePaletteChooser() {
		this.expanded_palettechooser = !this.expanded_palettechooser;
	},

	setColorpalette(colorpaletteName, targetUrl = null) {

		// console.log('set colorpalette', colorpaletteName,targetUrl);

		const storage = this.getStorage();
		if (storage) {
			storage.setItem('colorpalette', colorpaletteName);
		}

		const el = document.getElementById(`colorpalette-${colorpaletteName}`)

		// console.log('#log 2844',el);

		let box = el.getBoundingClientRect();
		// console.log('#log 7743', box);

		const el2 = document.createElement("div");
		document.body.appendChild(el2);
		el2.classList.add('absolute', 'overflow-visible', 'z-[60]');
		el2.style.left = box.left + 'px';
		el2.style.top = box.top + 'px';
		el2.style.width = (box.right - box.left) + 'px';
		el2.style.height = (box.bottom - box.top) + 'px';



		const el3 = document.createElement("div");
		el3.classList.add('absolute', 'inset-0', 'transition-all', 'duration-1000', 'ease-linear');

		el2.appendChild(el3);


		const cloneEl = el.cloneNode(true);

		const el4 = document.createElement("div");
		el4.classList.add('intro-palettes');
		el3.appendChild(el4)

		cloneEl.classList.add('cloned');
		el4.appendChild(cloneEl);


		setTimeout(() => {
			el3.classList.add('-left-[100vw]', '-right-[100vw]', 'overflow-hidden');
			el.blur();
		}, 10);

		if (options.template == "intro") {
			setTimeout(() => {
				if (targetUrl) {
					window.location = targetUrl;
				}
				setTimeout(() => {
					el2.remove()
				}, 5000);
			}, 1000);
		} else {
			setTimeout(() => {

				var body = document.getElementById('body');

				body.classList.remove('theme-' + this.currentColorpalette)
				this.currentColorpalette = colorpaletteName;
				body.classList.add('theme-' + this.currentColorpalette);
				if (targetUrl) {
					window.location = targetUrl;
				}

				setTimeout(() => {
					this.expanded_palettechooser = false;
				}, 500);
			}, 1000);



			setTimeout(() => {
				el3.classList.add('bottom-96', 'duration-200', 'ease-in-out');
			}, 1600);

			setTimeout(() => {
				el2.remove()
			}, 3000);
		}


	},
	getStorage() {
		if (this.storage == 'untested') {

			const storage = safeGetStorage();

			if (storage) {
				this.storage = storage
			} else {
				this.storage = null
			}
		}
		return this.storage;
	},
	getCurrentColorpaletteFromLocalStorage() {
		const storage = this.getStorage();
		if (storage) {
			return storage.getItem('colorpalette');
		}
		return null;
	},
	init() {
		// console.log('#log 1088 INIT88a');
		this.expanded_stripe_num = options.expanded_stripe_num || 0;

		if (this.expanded_stripe_num > 0 && options.template !== "homepage" && options.template !== "intro" && !(document.URL.includes('#'))) {
			this.$nextTick(() => {
				this.scrollToExpandedStripe();
			})
		}

		var colorpaletteFromLocalStorage = this.getCurrentColorpaletteFromLocalStorage();

		if (colorpaletteFromLocalStorage) {
			this.currentColorpalette = colorpaletteFromLocalStorage;
			body.classList.add('theme-' + this.currentColorpalette)
		} else {
			this.setColorpalette(options.colorpalettes[Math.floor(Math.random() * options.colorpalettes.length)]);
		}
	},
	scrollToExpandedStripe() {
		var el = document.getElementById('stripe_' + this.expanded_stripe_num);
		var top = el.offsetTop - 48;
		// console.log('scroll to stripe',this.expanded_stripe_num);
		document.body.scrollTo({
			top,
			behavior: 'smooth'
		});
	}

});
