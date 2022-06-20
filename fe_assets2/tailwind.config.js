const settingsFontSizes = require('../fe_assets/tailwind.settings.fontSizes')
const defaultTheme = require('tailwindcss/defaultTheme')


module.exports = {
    content: [
        "../public/fe_assets2/tw_classes.html"
    ],
    corePlugins: {
        preflight: false,
      },
    theme: {
        fontSize: settingsFontSizes,
        extend: {
            screens: {
                'sm': {
                    'min': '840px'
                },
                'md': {
                    'min': '900px'
                },
                'xs': {
                    'max': '839px'
                },
                'b640': {'min':'640px'}, 'bl640': {'max':'639px'}, 'b650': {'min':'650px'}, 'bl650': {'max':'649px'}, 'b660': {'min':'660px'}, 'bl660': {'max':'659px'}, 'b670': {'min':'670px'}, 'bl670': {'max':'669px'}, 'b680': {'min':'680px'}, 'bl680': {'max':'679px'}, 'b690': {'min':'690px'}, 'bl690': {'max':'689px'}, 'b700': {'min':'700px'}, 'bl700': {'max':'699px'}, 'b710': {'min':'710px'}, 'bl710': {'max':'709px'}, 'b720': {'min':'720px'}, 'bl720': {'max':'719px'}, 'b730': {'min':'730px'}, 'bl730': {'max':'729px'}, 'b740': {'min':'740px'}, 'bl740': {'max':'739px'}, 'b750': {'min':'750px'}, 'bl750': {'max':'749px'}, 'b760': {'min':'760px'}, 'bl760': {'max':'759px'}, 'b770': {'min':'770px'}, 'bl770': {'max':'769px'}, 'b780': {'min':'780px'}, 'bl780': {'max':'779px'}, 'b790': {'min':'790px'}, 'bl790': {'max':'789px'}, 'b800': {'min':'800px'}, 'bl800': {'max':'799px'}, 'b810': {'min':'810px'}, 'bl810': {'max':'809px'}, 'b820': {'min':'820px'}, 'bl820': {'max':'819px'}, 'b830': {'min':'830px'}, 'bl830': {'max':'829px'}, 'b840': {'min':'840px'}, 'bl840': {'max':'839px'}, 'b850': {'min':'850px'}, 'bl850': {'max':'849px'}, 'b860': {'min':'860px'}, 'bl860': {'max':'859px'}, 'b870': {'min':'870px'}, 'bl870': {'max':'869px'}, 'b880': {'min':'880px'}, 'bl880': {'max':'879px'}, 'b890': {'min':'890px'}, 
                'bl890': {'max':'889px'}, 'b900': {'min':'900px'}, 'bl900': {'max':'899px'}, 'b910': {'min':'910px'}, 'bl910': {'max':'909px'}, 'b920': {'min':'920px'}, 'bl920': {'max':'919px'}, 'b930': {'min':'930px'}, 'bl930': {'max':'929px'}, 'b940': {'min':'940px'}, 'bl940': {'max':'939px'}, 'b950': {'min':'950px'}, 'bl950': {'max':'949px'}, 'b960': {'min':'960px'}, 'bl960': {'max':'959px'}, 'b970': {'min':'970px'}, 'bl970': {'max':'969px'}, 'b980': {'min':'980px'}, 'bl980': {'max':'979px'}, 'b990': {'min':'990px'}, 'bl990': {'max':'989px'}, 'b1000': {'min':'1000px'}, 'bl1000': {'max':'999px'}, 'b1010': {'min':'1010px'}, 'bl1010': {'max':'1009px'}, 'b1020': {'min':'1020px'}, 'bl1020': {'max':'1019px'}, 'b1030': {'min':'1030px'}, 'bl1030': {'max':'1029px'}, 'b1040': {'min':'1040px'}, 'bl1040': {'max':'1039px'}, 'b1050': {'min':'1050px'}, 'bl1050': {'max':'1049px'}, 'b1060': {'min':'1060px'}, 'bl1060': {'max':'1059px'}, 'b1070': {'min':'1070px'}, 'bl1070': {'max':'1069px'}, 'b1080': {'min':'1080px'}, 'bl1080': {'max':'1079px'}, 'b1090': {'min':'1090px'}, 'bl1090': {'max':'1089px'}, 'b1100': {'min':'1100px'}, 'bl1100': {'max':'1099px'}, 'b1110': {'min':'1110px'}, 'bl1110': {'max':'1109px'}, 'b1120': {'min':'1120px'}, 'bl1120': {'max':'1119px'}, 'b1130': {'min':'1130px'}, 'bl1130': {'max':'1129px'}, 'b1140': {'min':'1140px'}, 'bl1140': {'max':'1139px'}, 'b1150': {'min':'1150px'}, 'bl1150': {'max':'1149px'}, 'b1160': {'min':'1160px'}, 'bl1160': {'max':'1159px'}, 'b1170': {'min':'1170px'}, 'bl1170': {'max':'1169px'}, 'b1180': {'min':'1180px'}, 'bl1180': {'max':'1179px'}, 'b1190': {'min':'1190px'}, 'bl1190': {'max':'1189px'}, 'b1200': {'min':'1200px'}, 'bl1200': {'max':'1199px'}
            }
        },
        fontFamily: {
                sans: ['Soleil-Regular', ...defaultTheme.fontFamily.sans],
                soleil: ['Soleil-Regular', ...defaultTheme.fontFamily.sans],
                soleil_book: ['Soleil-Book', ...defaultTheme.fontFamily.sans],
                soleil_light: ['Soleil-Light', ...defaultTheme.fontFamily.sans],
                soleil_bold: ['Soleil-Bold', ...defaultTheme.fontFamily.sans],
                garamond: ['Garamond-Regular', ...defaultTheme.fontFamily.sans],
        },
    },
}
