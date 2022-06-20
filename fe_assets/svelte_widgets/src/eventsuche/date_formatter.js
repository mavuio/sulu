export default {
    monthNames: [
      "Januar", "Februar", "März", "April", "Mai", "Juni",
      "Juli", "August", "September", "Oktober", "November", "Dezember"
    ],
    shortMonthNames: [
      "Jan.", "Feb.", "März", "April", "Mai", "Juni",
      "Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dez."
    ],
    dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
    formatDate: function (date, format, lang='en') {
      var self = this;
      format = self.getProperDigits(format, /d+/gi, date.getDate());
      format = self.getProperDigits(format, /M+/g, date.getMonth() + 1);
      format = format.replace(/y+/gi, function (y) {
        var len = y.length;
        var year = date.getFullYear();
        if (len == 2)
          return (year + "").slice(-2);
        else if (len == 4)
          return year;
        return y;
      })
      format = self.getProperDigits(format, /H+/g, date.getHours());
      format = self.getProperDigits(format, /h+/g, self.getHours12(date.getHours()));
      format = self.getProperDigits(format, /m+/g, date.getMinutes());
      format = self.getProperDigits(format, /s+/gi, date.getSeconds());
      format = format.replace(/a/ig, function (a) {
        var amPm = self.getAmPm(date.getHours())
        if (a === 'A')
          return amPm.toUpperCase();
        return amPm;
      })
      format = self.getFullOr3Letters(lang,format, /d+/gi, self.dayNames, date.getDay())
      format = self.getFullOr3LetterMonth(lang,format, /M+/g, date.getMonth())
      return format;
    },
    getProperDigits: function (format, regex, value) {
      return format.replace(regex, function (m) {
        var length = m.length;
        if (length == 1)
          return value;
        else if (length == 2)
          return ('0' + value).slice(-2);
        return m;
      })
    },
    getHours12: function (hours) {
      // https://stackoverflow.com/questions/10556879/changing-the-1-24-hour-to-1-12-hour-for-the-gethours-method
      return (hours + 24) % 12 || 12;
    },
    getAmPm: function (hours) {
      // https://stackoverflow.com/questions/8888491/how-do-you-display-javascript-datetime-in-12-hour-am-pm-format
      return hours >= 12 ? 'pm' : 'am';
    },
    getFullOr3Letters: function (lang,format, regex, nameArray, value) {

      const targetLen= (lang=='de')?2:3;
      return format.replace(regex, function (s) {
        var len = s.length;
        if (len == 3)
          return nameArray[value].substr(0, targetLen);
        else if (len == 4)
          return nameArray[value];
        return s;
      })
    },
    getFullOr3LetterMonth: function (lang,format, regex, value) {
      var self = this;

      const targetLen= (lang=='de')?2:3;
      return format.replace(regex, function (s) {
        var len = s.length;
        if (len == 3)
          return self.shortMonthNames[value];
        else if (len == 4)
          return self.monthNames[value];
        return s;
      })
    }
  };

  
// console.log(DateFormatter.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss'));
// console.log(DateFormatter.formatDate(new Date(), 'D DD DDD DDDD, M MM MMM MMMM, YY YYYY, h hh H HH, m mm, s ss, a A'));
// console.log(DateFormatter.formatDate(new Date(2005, 1, 2, 3, 4, 5), 'D DD DDD DDDD, M MM MMM MMMM, YY YYYY, h hh H HH, m mm, s ss, a A'));

