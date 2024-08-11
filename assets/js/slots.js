$(document).ready(function() {
    let sessionCredits = 10;
    // call to /start to get the initial credits
    $.post('/start', function(response) {
        sessionCredits = response.credits;
        $('#credits').text('Credits: ' + sessionCredits);
    }).fail(function() {
        $('#message').text('Failed to get the initial credits. Please try again.');
    });

    $('#spin-button').on('click', function() {
        // disable the spin button and make him grey
        $('#spin-button').prop('disabled', true);
        $('#spin-button').css('background-color', 'grey');
        spinSlots();
    });

    $('#cashout-button').on('click', function() {
        cashOut();
    });

    function spinSlots() {
        // sets slots all to ? emojin before spinning
        $('#slot1').text('❓');
        $('#slot2').text('❓');
        $('#slot3').text('❓');
        $.post('/roll', function(response) {
            stopSpinning(response.symbols, response.credits, response.win, response.reward);
        }).fail(function() {
            $('#message').text('Failed to spin the slots. Please try again.');
        });
    }

    function stopSpinning(symbols, credits, win, reward) {
        setTimeout(function() {
            $('#slot1').removeClass('spinning').text(symbols[0]);
        }, 1000);
        setTimeout(function() {
            $('#slot2').removeClass('spinning').text(symbols[1]);
        }, 2000);
        setTimeout(function() {
            $('#slot3').removeClass('spinning').text(symbols[2]);
            sessionCredits = credits;
            $('#credits').text('Credits: ' + sessionCredits);
            $('#message').text(win ? 'You won ' + reward + ' credits!' : 'You lost 1 credit.');
            $('#spin-button').prop('disabled', false);
            $('#spin-button').css('background-color', '#007bff');
        }, 3000);
    }

    function cashOut() {
        $.post('/cashout', function(response) {
            $('#message').text('You cashed out ' + response.cashed_out + ' credits.');
            sessionCredits = 0;
            $('#credits').text('Credits: ' + sessionCredits);
        }).fail(function() {
            $('#message').text('Failed to cash out. Please try again.');
        });
    }
});