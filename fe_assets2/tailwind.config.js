const baseconf = require('../fe_assets/tailwind.config')

module.exports = {
    ...baseconf,
    content: [
        "../public/fe_assets2/tw_classes.html"
    ],
    corePlugins: {
        preflight: false,
      },
}
