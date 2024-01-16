let N = 5;
let data;

/**
 * Gets leads object and returns processed with formatted keys and values
 * @param   {Object} data
 * @returns {Object}
 * */
function processData(data) {
    let processedData = {};
    let processedKey;
    for (let key in data) {
        processedKey = formatDate(new Date(key * 1000));
        if (data[key] >= N) {
            processedData[processedKey] = 0;
        } else {
            processedData[processedKey] = 1;
        }
    }
    return processedData;
}

/**
 * Gets date and returns YYYY-mm-dd string
 * @param   {Date} date
 * @returns {String}
 * */
function formatDate(date) {
    return date.toISOString().slice(0, 10);
}

$(window).load(function () {
    // get data from php script
    $.ajax({
        url: 'index.php',
        type: 'GET',
        success: function (result) {
            data = JSON.parse(result);

            // rewrite data with needed values
            let datesProcessed = (processData(data));

            // make dates for loop today+offset < dateFuture
            let today = new Date();
            let dateFuture = new Date(new Date().setDate(today.getDate() + 30)) // date 30 days in the future

            let date = today;
            let selectableDates = []

            // populate selectableDates array
            for (let dayOffset = 0; date < dateFuture; dayOffset++) {
                date = new Date(new Date().setDate(today.getDate() + dayOffset));
                dateFormatted = formatDate(date);
                if (datesProcessed[dateFormatted] !== 0) {
                    selectableDates.push({date: date});
                }
            }
            // initialize datepicker
            $('#mydate').glDatePicker({
                allowYearSelect: false,
                allowMonthSelect: false,
                selectableDates: selectableDates
            });
        },
        error: function () {
            console.log('error');
        }
    })

});