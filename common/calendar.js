calDateFormat = "yyyyMMDD";
monthArray = new Array('January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December');
monthShort = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
today = new Date();

//Variables to set Year Range
earlyYear=1900;
earlyYear = today.getFullYear();

lateYear=2015;
lateYear = today.getFullYear() + 12;

windowOpen="N";
fullUpdate="Y";
updateCall = null;

// CALENDAR COLORS
topBackground    = "#EEEEEE";         // BG COLOR OF THE TOP FRAME
bottomBackground = "#EEEEEE";         // BG COLOR OF THE BOTTOM FRAME
tableBGColor     = "#CCCCCC";         // BG COLOR OF THE BOTTOM FRAME'S TABLE
cellColor        = "white";     // TABLE CELL BG COLOR OF THE DATE CELLS IN THE BOTTOM FRAME
headingCellColor = "#99CA3C";         // TABLE CELL BG COLOR OF THE WEEKDAY ABBREVIATIONS
headingTextColor = "#FFFFFF";         // TEXT COLOR OF THE WEEKDAY ABBREVIATIONS
dateColor        = "#666666";          // TEXT COLOR OF THE LISTED DATES (1-28+)
focusColor       = "#FF0000";       // TEXT COLOR OF THE SELECTED DATE (OR CURRENT DATE)
hoverColor       = "#000000";       // TEXT COLOR OF A LINK WHEN YOU HOVER OVER IT
fontStyle        = "11px arial, helvetica, sans-serif";           // TEXT STYLE FOR DATES
headingFontStyle = "11px arial, helvetica, sans-serif";      // TEXT STYLE FOR WEEKDAY ABBREVIATIONS

// FORMATTING PREFERENCES
bottomBorder  = false;        // TRUE/FALSE (WHETHER TO DISPLAY BOTTOM CALENDAR BORDER)
tableBorder   = 0;            // SIZE OF CALENDAR TABLE BORDER (BOTTOM FRAME) 0=none


var isNav = false;
var isIE  = false;

// ASSUME IT'S EITHER NETSCAPE OR MSIE
if (navigator.appName == "Netscape") {
    isNav = true;
}
else {
    isIE = true;
}

// PRE-BUILD PORTIONS OF THE CALENDAR WHEN THIS JS LIBRARY LOADS INTO THE BROWSER
buildCalParts();



// CALENDAR FUNCTIONS BEGIN HERE ---------------------------------------------------

function setUpdateCall(func)
{
    updateCall = func;
}


// SET THE INITIAL VALUE OF THE GLOBAL DATE FIELD
function setDateField(dayField,monthField,yearField,oracleField) {
    // ASSIGN THE INCOMING FIELD OBJECT TO A GLOBAL VARIABLE
    calOracleField = oracleField;
    calDayField = dayField;
    calMonthField = monthField;
    calYearField = yearField;

    // SET calDate TO THE DATE IN THE INCOMING FIELD OR DEFAULT TO TODAY'S DATE
    setInitialDate(oracleField.value);
    //alert(oracleField.value);

    // THE CALENDAR FRAMESET DOCUMENTS ARE CREATED BY JAVASCRIPT FUNCTIONS
    calDocTop    = buildTopCalFrame();
    calDocBottom = buildBottomCalFrame();

    fullUpdate="Y";
    windowOpen="Y";
}


// SET THE INITIAL CALENDAR DATE TO TODAY OR TO THE EXISTING VALUE IN dateField
function setInitialDate(inDate) {

    calDate = new Date();
    var year = parseInt(inDate.substring(0,4));
    var month = parseInt(inDate.substring(4,5))*10 + parseInt(inDate.substring(5,6)-1);
    var day = parseInt(inDate.substring(6,7))*10 + parseInt(inDate.substring(7,8));
    calDate.setMonth(month);
    calDate.setYear(year);
    //Strange workaround to get round parseInt problems for the 8th & 9th of the month
    calDate.setDate(day);

    calDay = calDate.getDate();

    calDate.setDate(1);
}


// CREATE THE TOP CALENDAR FRAME
function buildTopCalFrame() {
    // CREATE THE TOP FRAME OF THE CALENDAR
    var calDoc =
        "<HTML>" +
        "<HEAD>" +
        "</HEAD>" +
        "<BODY BGCOLOR='" + topBackground + "'>" +
        "<FORM NAME='calControl' onSubmit='return false;'>" +
        "<CENTER>" +
        "<TABLE CELLPADDING=0 CELLSPACING=1 BORDER=0>" +
        "<TR><TD COLSPAN=7>" +
        "<CENTER>" +
        getMonthSelect() +
        "<SELECT NAME='year' onChange='parent.opener.setYear()' STYLE='font:" + fontStyle + "'>";

      selectedYear = calDate.getFullYear();
      for(i=earlyYear;i<=lateYear;i++)
      {
        if(i==selectedYear)
        {
          calDoc+="<OPTION VALUE='"+i+"' SELECTED>"+i+"</OPTION>";
        }
        else
        {
          calDoc+="<OPTION VALUE='"+i+"'>"+i+"</OPTION>";
        }
      }
      calDoc+= "</SELECT>" +
        "</CENTER>" +
        "</TD>" +
        "</TR>" +
        "</TABLE>" +
        "</CENTER>" +
        "</FORM>" +
        "</BODY>" +
        "</HTML>";

    return calDoc;
}


// CREATE THE BOTTOM CALENDAR FRAME
// (THE MONTHLY CALENDAR)
function buildBottomCalFrame() {

    // START CALENDAR DOCUMENT
    var calDoc = calendarBegin;

    // GET MONTH, AND YEAR FROM GLOBAL CALENDAR DATE
    month   = calDate.getMonth();
    year    = calDate.getFullYear();


    // GET GLOBALLY-TRACKED DAY VALUE (PREVENTS JAVASCRIPT DATE ANOMALIES)
    day     = calDay;

    var i   = 0;

    // DETERMINE THE NUMBER OF DAYS IN THE CURRENT MONTH
    var days = getDaysInMonth();

    // IF GLOBAL DAY VALUE IS > THAN DAYS IN MONTH, HIGHLIGHT LAST DAY IN MONTH
    if (day > days) {
        day = days;
    }

    // DETERMINE WHAT DAY OF THE WEEK THE CALENDAR STARTS ON
    var firstOfMonth = new Date (year, month, 1);

    // GET THE DAY OF THE WEEK THE FIRST DAY OF THE MONTH FALLS ON
    var startingPos  = firstOfMonth.getDay();
    days += startingPos;

    // KEEP TRACK OF THE COLUMNS, START A NEW ROW AFTER EVERY 7 COLUMNS
    var columnCount = 0;

    // MAKE BEGINNING NON-DATE CELLS BLANK
    for (i = 0; i < startingPos; i++) {

        calDoc += blankCell;
    columnCount++;
    }

    // SET VALUES FOR DAYS OF THE MONTH
    var currentDay = 0;
    var dayType    = "weekday";

    // DATE CELLS CONTAIN A NUMBER
    for (i = startingPos; i < days; i++) {

    var paddingChar = "&nbsp;";

        // ADJUST SPACING SO THAT ALL LINKS HAVE RELATIVELY EQUAL WIDTHS
        if (i-startingPos+1 < 10) {
            padding = "&nbsp;&nbsp;";
        }
        else {
            padding = "&nbsp;";
        }

        // GET THE DAY CURRENTLY BEING WRITTEN
        currentDay = i-startingPos+1;

        // SET THE TYPE OF DAY, THE focusDay GENERALLY APPEARS AS A DIFFERENT COLOR
        if (currentDay == day) {
            dayType = "focusDay";
        }
        else {
            dayType = "weekDay";
        }

        // ADD THE DAY TO THE CALENDAR STRING
        calDoc += "<TD align=center bgcolor='" + cellColor + "'>" +
                  "<a class='" + dayType + "' href='javascript:parent.opener.returnDate(" +
                  currentDay + ")'>" + padding + currentDay + paddingChar + "</a></TD>";

        columnCount++;

        // START A NEW ROW WHEN NECESSARY
        if (columnCount % 7 == 0) {
            calDoc += "</TR><TR>";
        }
    }

    // MAKE REMAINING NON-DATE CELLS BLANK
    for (i=days; i<42; i++)  {

        calDoc += blankCell;
    columnCount++;

        // START A NEW ROW WHEN NECESSARY
        if (columnCount % 7 == 0) {
            calDoc += "</TR>";
            if (i<41) {
                calDoc += "<TR>";
            }
        }
    }

    // FINISH THE NEW CALENDAR PAGE
    calDoc += calendarEnd;

    // RETURN THE COMPLETED CALENDAR PAGE
    return calDoc;
}


// WRITE THE MONTHLY CALENDAR TO THE BOTTOM CALENDAR FRAME
function writeCalendar() {

    // CREATE THE NEW CALENDAR FOR THE SELECTED MONTH & YEAR
    calDocBottom = buildBottomCalFrame();

    // WRITE THE NEW CALENDAR TO THE BOTTOM FRAME
      self.newWin.frames['bottomCalFrame'].document.open();
      self.newWin.frames['bottomCalFrame'].document.write(calDocBottom);
      self.newWin.frames['bottomCalFrame'].document.close();
}


// SET THE CALENDAR TO TODAY'S DATE AND DISPLAY THE NEW CALENDAR
function setToday() {

    // SET GLOBAL DATE TO TODAY'S DATE
    calDate = new Date();

    // SET DAY MONTH AND YEAR TO TODAY'S DATE
    var month = calDate.getMonth();
    var year  = calDate.getFullYear();

    // SET MONTH IN DROP-DOWN LIST
    self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex = month;

    // SET YEAR VALUE
    self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = year-earlyYear;

    // DISPLAY THE NEW CALENDAR
    writeCalendar();

}


// SET THE GLOBAL DATE TO THE NEWLY ENTERED YEAR AND REDRAW THE CALENDAR
function setYear() {

    // GET THE NEW YEAR VALUE
    var year  = self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex+earlyYear;

    // IF IT'S A FOUR-DIGIT YEAR THEN CHANGE THE CALENDAR
    //if (isFourDigitYear(year)) {
        calDate.setFullYear(year);
        writeCalendar();
    //}
    //else {
        // HIGHLIGHT THE YEAR IF THE YEAR IS NOT FOUR DIGITS IN LENGTH
        //self.newWin.frames['topCalFrame'].document.calControl.year.focus();
        //self.newWin.frames['topCalFrame'].document.calControl.year.select();
    //}
}


// SET THE GLOBAL DATE TO THE SELECTED MONTH AND REDRAW THE CALENDAR
function setCurrentMonth() {

    // GET THE NEWLY SELECTED MONTH AND CHANGE THE CALENDAR ACCORDINGLY
    var month = self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex;

    calDate.setMonth(month);
    writeCalendar();
}


// SET THE GLOBAL DATE TO THE PREVIOUS YEAR AND REDRAW THE CALENDAR
function setPreviousYear() {

    var year  = self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex+earlyYear;

    if (isFourDigitYear(year) && year > 1000) {
        year--;
        calDate.setFullYear(year);
        self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = year-earlyYear;
        writeCalendar();
    }
}


// SET THE GLOBAL DATE TO THE PREVIOUS MONTH AND REDRAW THE CALENDAR
function setPreviousMonth() {

    var year  = self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex+earlyYear;
    if (isFourDigitYear(year)) {
        var month = self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex;

        // IF MONTH IS JANUARY, SET MONTH TO DECEMBER AND DECREMENT THE YEAR
        if (month == 0) {
            month = 11;
            if (year > 1000) {
                year--;
                calDate.setFullYear(year);
                self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = year-earlyYear;
            }
        }
        else {
            month--;
        }
        calDate.setMonth(month);
        self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex = month;
        writeCalendar();
    }
}


// SET THE GLOBAL DATE TO THE NEXT MONTH AND REDRAW THE CALENDAR
function setNextMonth() {

    var year = self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex+earlyYear;

    if (isFourDigitYear(year)) {
        var month = self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex;

        // IF MONTH IS DECEMBER, SET MONTH TO JANUARY AND INCREMENT THE YEAR
        if (month == 11) {
            month = 0;
            year++;
            calDate.setFullYear(year);
            self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = year-earlyYear;
        }
        else {
            month++;
        }
        calDate.setMonth(month);
        self.newWin.frames['topCalFrame'].document.calControl.month.selectedIndex = month;
        writeCalendar();
    }
}


// SET THE GLOBAL DATE TO THE NEXT YEAR AND REDRAW THE CALENDAR
function setNextYear() {

    var year  = self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex+earlyYear;
    if (isFourDigitYear(year)) {
        year++;
        calDate.setFullYear(year);
        self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = year-earlyYear;
        writeCalendar();
    }
}

function getDaysInMonth()  {

    var days;
    var month = calDate.getMonth()+1;
    var year  = calDate.getFullYear();

    // RETURN 31 DAYS
    if (month==1 || month==3 || month==5 || month==7 || month==8 ||
        month==10 || month==12)  {
        days=31;
    }
    // RETURN 30 DAYS
    else if (month==4 || month==6 || month==9 || month==11) {
        days=30;
    }
    // RETURN 29 DAYS
    else if (month==2)  {
        if (isLeapYear(year)) {
            days=29;
        }
        // RETURN 28 DAYS
        else {
            days=28;
        }
    }
    return (days);
}

function isLeapYear(Year) {
    if (((Year % 4)==0) && ((Year % 100)!=0) || ((Year % 400)==0)) {
        return (true);
    }
    else
    {
        return (false);
    }
}

function isFourDigitYear(year) {

    if (year.length != 4) {
        self.newWin.frames['topCalFrame'].document.calControl.year.selectedIndex = calDate.getFullYear();
        self.newWin.frames['topCalFrame'].document.calControl.year.select();
        self.newWin.frames['topCalFrame'].document.calControl.year.focus();
    }
    else {
        return true;
    }
}


// BUILD THE MONTH SELECT LIST
function getMonthSelect() {


    // DETERMINE MONTH TO SET AS DEFAULT
    var activeMonth = calDate.getMonth();

    // START HTML SELECT LIST ELEMENT
    monthSelect = "<SELECT NAME='month' onChange='parent.opener.setCurrentMonth()' STYLE='font:" + fontStyle + "'>";

    // LOOP THROUGH MONTH ARRAY
    for (i in monthArray) {

        // SHOW THE CORRECT MONTH IN THE SELECT LIST
        if (i == activeMonth) {
            monthSelect += "<OPTION SELECTED>" + monthArray[i] + "\n";
        }
        else {
            monthSelect += "<OPTION>" + monthArray[i] + "\n";
        }
    }
    monthSelect += "</SELECT>";

    // RETURN A STRING VALUE WHICH CONTAINS A SELECT LIST OF ALL 12 MONTHS
    return monthSelect;
}

// PRE-BUILD PORTIONS OF THE CALENDAR (FOR PERFORMANCE REASONS)
function buildCalParts() {

    weekdayList  = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    weekdayArray = new Array('Su','Mo','Tu','We','Th','Fr','Sa');
    var weekdays = "<TR BGCOLOR='" + headingCellColor + "'>";

    for (i in weekdayArray) {
        weekdays += "<TD class='heading' align=center>" + weekdayArray[i] + "</TD>";
    }
    weekdays += "</TR>";

    blankCell = "<TD align=center bgcolor='" + cellColor + "'>&nbsp;&nbsp;&nbsp;</TD>";

    // BUILD THE TOP PORTION OF THE CALENDAR PAGE USING CSS TO CONTROL SOME DISPLAY ELEMENTS
    calendarBegin =
        "<HTML>" +
        "<HEAD>" +
        "<STYLE type='text/css'>" +
        "<!--" +
        "TD.heading { text-decoration: none; color:" + headingTextColor + "; font: " + headingFontStyle + "; }" +
        "A.focusDay:link { color: " + focusColor + "; text-decoration: none; font: " + fontStyle + "; }" +
        "A.focusDay:visited { color: " + focusColor + "; text-decoration: none; font: " + fontStyle + "; }" +
        "A.focusDay:hover { color: " + focusColor + "; text-decoration: none; font: " + fontStyle + "; }" +
        "A.weekday:link { color: " + dateColor + "; text-decoration: none; font: " + fontStyle + "; }" +
        "A.weekday:visited { color: " + dateColor + "; text-decoration: none; font: " + fontStyle + "; }" +
        "A.weekday:hover { color: " + hoverColor + "; font: " + fontStyle + "; }" +
        "-->" +
        "</STYLE>" +
        "</HEAD>" +
        "<BODY BGCOLOR='" + bottomBackground + "'" +
        "<CENTER>";

        // NAVIGATOR NEEDS A TABLE CONTAINER TO DISPLAY THE TABLE OUTLINES PROPERLY
        if (isNav) {
            calendarBegin +=
                "<TABLE CELLPADDING=0 CELLSPACING=1 BORDER=" + tableBorder + " ALIGN=CENTER BGCOLOR='" + tableBGColor + "'><TR><TD>";
        }

        // BUILD WEEKDAY HEADINGS
        calendarBegin +=
            "<TABLE CELLPADDING=0 CELLSPACING=1 BORDER=" + tableBorder + " ALIGN=CENTER BGCOLOR='" + tableBGColor + "'>" +
            weekdays +
            "<TR>";


    // BUILD THE BOTTOM PORTION OF THE CALENDAR PAGE
    calendarEnd = "";

        // WHETHER OR NOT TO DISPLAY A THICK LINE BELOW THE CALENDAR
        if (bottomBorder) {
            calendarEnd += "<TR></TR>";
        }

        // NAVIGATOR NEEDS A TABLE CONTAINER TO DISPLAY THE BORDERS PROPERLY
        if (isNav) {
            calendarEnd += "</TD></TR></TABLE>";
        }

        // END THE TABLE AND HTML DOCUMENT
        calendarEnd +=
            "</TABLE>" +
            "</CENTER>" +
            "</BODY>" +
            "</HTML>";
}



// example:  jsReplace("To be or not to be", "be", "ski");
//           result: "To ski or not to ski"
//
function jsReplace(inString, find, replace) {

    var outString = "";

    if (!inString) {
        return "";
    }

    // REPLACE ALL INSTANCES OF find WITH replace
    if (inString.indexOf(find) != -1) {
        // SEPARATE THE STRING INTO AN ARRAY OF STRINGS USING THE VALUE IN find
        t = inString.split(find);

        // JOIN ALL ELEMENTS OF THE ARRAY, SEPARATED BY THE VALUE IN replace
        return (t.join(replace));
    }
    else {
        return inString;
    }
}

function doNothing() {
}

function makeTwoDigit(inValue) {

    var numVal = parseInt(inValue, 10);

    // VALUE IS LESS THAN TWO DIGITS IN LENGTH
    if (numVal < 10) {

        // ADD A LEADING ZERO TO THE VALUE AND RETURN IT
        return("0" + numVal);
    }
    else {
        return numVal;
    }
}

function updateDate(dayField,monthField,yearField,oracleField) {

    windowOpen="N";
    fullUpdate="Y";
    calOracleField = oracleField;
    calDayField = dayField;
    calMonthField = monthField;
    calYearField = yearField;

    if (calDayField.type=="select-one"){
      inDay = calDayField.selectedIndex+1;
    }
    else {
      inDay = calDayField.value;
    }

    calDate = new Date();
    calDate.setDate(1);
    calDate.setMonth(calMonthField.selectedIndex);
    calDate.setYear(calYearField.selectedIndex+earlyYear);

    var mDays = getDaysInMonth();
    if(inDay > mDays)
    {
	calDayField.value = mDays;
	inDay = mDays;
    }

    returnDate(inDay);

}

function buildDaySelector(dateField)
{
  fullUpdate="N";
  setInitialDate(dateField.value);
  calOracleField=dateField;

  dayCode="";
  var i;
  for(i=1; i<=31; i++)
  {
    if(calDay==i)
    {
      dayCode+="<option value="+makeTwoDigit(i)+" selected>"+i+"</option>\n";
    }
    else
    {
      dayCode+="<option value="+makeTwoDigit(i)+">"+i+"</option>\n";
    }
  }
  returnDate(calDay);
  return dayCode;
}

function buildMonthSelector(dateField)
{
  fullUpdate="N";
  setInitialDate(dateField.value);
  calOracleField=dateField;

  monthCode="";
  var i;
  for(i=0;i<12;i++)
  {
    if(calDate.getMonth()==i)
    {
      monthCode+="<option value="+monthShort[i]+" selected>"+monthShort[i]+"</option>\n";
    }
    else
    {
      monthCode+="<option value="+monthShort[i]+">"+monthShort[i]+"</option>\n";
    }
  }
  returnDate(calDay);
  return monthCode;
}

function buildYearSelector(dateField)
{
  fullUpdate="N";
  setInitialDate(dateField.value);
  calOracleField=dateField;

  yearCode="";
  var i;
  for(i=earlyYear;i<=lateYear;i++)
  {
    if(calDate.getFullYear()==i)
    {
      yearCode+="<option value="+i+" selected>"+i+"</option>\n";
    }
    else
    {
      yearCode+="<option value="+i+">"+i+"</option>\n";
    }
  }
  //alert(yearCode);
  returnDate(calDay);
  return yearCode;
}

function setOracleField(hiddenOracleField)
{
//  hiddenOracleField.value=calDay+"-"+monthShort[calDate.getMonth()].toUpperCase()+"-"+calDate.getFullYear();
    var year = calDate.getFullYear();
    var month = calDate.getMonth() + 1;
    var day = calDay;
    month = (month > 9) ? month : "0" + month;
    day = (day > 9) ? day : "0" + day;
    hiddenOracleField.value = "" + year + month + day; 
}

// SET FIELD VALUE TO THE DATE SELECTED AND CLOSE THE CALENDAR WINDOW
function returnDate(inDay)
{
    // inDay = THE DAY THE USER CLICKED ON
    calDate.setDate(inDay);

    // SET THE DATE RETURNED TO THE USER
    var day           = calDate.getDate();
    var month         = calDate.getMonth()+1;
    var year          = calDate.getFullYear();
    var monthString   = monthArray[calDate.getMonth()];
    var monthAbbrev   = monthString.substring(0,3);
    var weekday       = weekdayList[calDate.getDay()];
    var weekdayAbbrev = weekday.substring(0,3);

    outDate = calDateFormat;

    // RETURN TWO DIGIT DAY
    if (calDateFormat.indexOf("DD") != -1) {
        day = makeTwoDigit(day);
        outDate = jsReplace(outDate, "DD", day);
    }
    // RETURN ONE OR TWO DIGIT DAY
    else if (calDateFormat.indexOf("dd") != -1) {
        outDate = jsReplace(outDate, "dd", day);
    }

    // RETURN TWO DIGIT MONTH
    if (calDateFormat.indexOf("MM") != -1) {
        month = makeTwoDigit(month);
        outDate = jsReplace(outDate, "MM", month);
    }
    // RETURN ONE OR TWO DIGIT MONTH
    else if (calDateFormat.indexOf("mm") != -1) {
        outDate = jsReplace(outDate, "mm", month);
    }

    // RETURN FOUR-DIGIT YEAR
    if (calDateFormat.indexOf("yyyy") != -1) {
        outDate = jsReplace(outDate, "yyyy", year);
    }
    // RETURN TWO-DIGIT YEAR
    else if (calDateFormat.indexOf("yy") != -1) {
        var yearString = "" + year;
        var yearString = yearString.substring(2,4);
        outDate = jsReplace(outDate, "yy", yearString);
    }
    // RETURN FOUR-DIGIT YEAR
    else if (calDateFormat.indexOf("YY") != -1) {
        outDate = jsReplace(outDate, "YY", year);
    }

    // RETURN DAY OF MONTH (Initial Caps)
    if (calDateFormat.indexOf("Month") != -1) {
        outDate = jsReplace(outDate, "Month", monthString);
    }
    // RETURN DAY OF MONTH (lowercase letters)
    else if (calDateFormat.indexOf("month") != -1) {
        outDate = jsReplace(outDate, "month", monthString.toLowerCase());
    }
    // RETURN DAY OF MONTH (UPPERCASE LETTERS)
    else if (calDateFormat.indexOf("MONTH") != -1) {
        outDate = jsReplace(outDate, "MONTH", monthString.toUpperCase());
    }

    // RETURN DAY OF MONTH 3-DAY ABBREVIATION (Initial Caps)
    if (calDateFormat.indexOf("Mon") != -1) {
        outDate = jsReplace(outDate, "Mon", monthAbbrev);
    }
    // RETURN DAY OF MONTH 3-DAY ABBREVIATION (lowercase letters)
    else if (calDateFormat.indexOf("mon") != -1) {
        outDate = jsReplace(outDate, "mon", monthAbbrev.toLowerCase());
    }
    // RETURN DAY OF MONTH 3-DAY ABBREVIATION (UPPERCASE LETTERS)
    else if (calDateFormat.indexOf("MON") != -1) {
        outDate = jsReplace(outDate, "MON", monthAbbrev.toUpperCase());
    }

    // RETURN WEEKDAY (Initial Caps)
    if (calDateFormat.indexOf("Weekday") != -1) {
        outDate = jsReplace(outDate, "Weekday", weekday);
    }
    // RETURN WEEKDAY (lowercase letters)
    else if (calDateFormat.indexOf("weekday") != -1) {
        outDate = jsReplace(outDate, "weekday", weekday.toLowerCase());
    }
    // RETURN WEEKDAY (UPPERCASE LETTERS)
    else if (calDateFormat.indexOf("WEEKDAY") != -1) {
        outDate = jsReplace(outDate, "WEEKDAY", weekday.toUpperCase());
    }

    // RETURN WEEKDAY 3-DAY ABBREVIATION (Initial Caps)
    if (calDateFormat.indexOf("Wkdy") != -1) {
        outDate = jsReplace(outDate, "Wkdy", weekdayAbbrev);
    }
    // RETURN WEEKDAY 3-DAY ABBREVIATION (lowercase letters)
    else if (calDateFormat.indexOf("wkdy") != -1) {
        outDate = jsReplace(outDate, "wkdy", weekdayAbbrev.toLowerCase());
    }
    // RETURN WEEKDAY 3-DAY ABBREVIATION (UPPERCASE LETTERS)
    else if (calDateFormat.indexOf("WKDY") != -1) {
        outDate = jsReplace(outDate, "WKDY", weekdayAbbrev.toUpperCase());
    }

    calOracleField.value = outDate;

    if(fullUpdate=="Y")
    {
      calDayField.selectedIndex = day-1;
      calMonthField.selectedIndex = month-1;
      calYearField.selectedIndex = year-earlyYear;
    }

    if(windowOpen=="Y")
    {
      // CLOSE THE CALENDAR WINDOW
      self.newWin.blur();
      self.newWin.close();

    }
    if (updateCall != null) {
        eval(updateCall);
    }
}


// my functions
function updateDateField(formName, fieldName, jsDate)
{
    var frm = document[formName];
    var year  = jsDate.getFullYear();
    var month = jsDate.getMonth() + 1;
    var day   = jsDate.getDate();
    frm["_day_" + fieldName].selectedIndex   = day - 1;
    frm["_month_" + fieldName].selectedIndex = month - 1;
    frm["_year_" + fieldName].selectedIndex  = year - earlyYear;
    month = (String(month).length == 2) ? month : "0" + month;
    day = (String(day).length == 2) ? day : "0" + day;
    var date = "" + year + month + day;
    frm[fieldName].value = date;
}
// date manipulation functions
function getJSDate(jsDate)
{
    var year  = jsDate.getFullYear();
    var month = jsDate.getMonth() + 1;
    var day   = jsDate.getDate();
    month = (String(month).length == 2) ? month : "0" + month;
    day = (String(day).length == 2) ? day : "0" + day;
    return "" + year + month + day;
}

function getStringDate(date)
{
    return (date.substring(0, 4) + "/" + 
            date.substring(4, 6) + "/" + 
            date.substring(6, 8));
}
