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
        spinSlots();
    });

    $('#cashout-button').on('click', function() {
        cashOut();
    });

    function spinSlots() {
        startSpinning();

        $.post('/roll', function(response) {
            stopSpinning(response.symbols, response.credits, response.win, response.reward);
        }).fail(function() {
            $('#message').text('Failed to spin the slots. Please try again.');
        });
    }

    function startSpinning() {
        $('.slot').addClass('spinning');
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