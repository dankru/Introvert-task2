let N = 5;
let data;

function processData(data) {
    let processedData = {};
    let processedKey;
    data.map((object) => {
        for (let key in object) {
            processedKey = formatDate(new Date(key * 1000));
            if (object[key] >= N) {
                processedData[processedKey] = 0;
            } else {
                processedData[processedKey] = 1;
            }
        }
    });
    return processedData;
}

function formatDate(date) {
    return date.toISOString().slice(0,10);
}

$(window).load(function () {

    $.ajax({
        url: 'index.php',
        type: 'GET',
        success: function (result) {
            let dataArray = [];
            data = JSON.parse(result);
            for (let i in data) {
                dataArray.push(data)
            }

            let datesProcessed = (processData(dataArray));

            let selectableDates = []

            let today = new Date();
            let dateFuture = new Date(new Date().setDate(today.getDate() + 30)) // date 30 days in the future
            let date = today;
            for (let dayOffset = 0; date < dateFuture; dayOffset++) {
                date = new Date(new Date().setDate(today.getDate() + dayOffset));
                dateFormatted = formatDate(date);
                if (datesProcessed[dateFormatted] !== 0) {
                    selectableDates.push({date: date});
                }
            }
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