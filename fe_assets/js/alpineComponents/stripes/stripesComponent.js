export default (options) => ({
	main_expanded: true,
	expanded_stripe_num: 0,
	init() {
	this.expanded_stripe_num = options.expanded_stripe_num || 0;
	console.log('#log 4863 stripes inited');
	},
	openStripe(stripenum, url) {
	this.expanded_stripe_num = stripenum;


	let prerenderLink = document.createElement("link");
	prerenderLink.setAttribute("rel", "prefetch");
	prerenderLink.setAttribute("href", url);
	document.head.appendChild(prerenderLink);


	setTimeout(() => {
	window.location = url
	}, 1000);


	},

});
