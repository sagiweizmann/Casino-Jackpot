$(document).ready(function() {
    let sessionCredits = 0;
    // call to /start to get the initial credits
    $.post('/start', function(response) {
        sessionCredits = response.credits;
        $('#credits').text('Credits: ' + sessionCredits);
    }).fail(function() {
        $('#message').text('Failed to get the initial credits. Please try again.');
    });

    init();

    $('#spin-button').on('click', function() {
        if (sessionCredits === 0) {
            $('#message').text('You do not have enough credits to spin the slots.');
        } else {
            spin();
        }
        $('#spin-button').prop('disabled', true);
        $('#spin-button').css('background-color', 'grey');

    });

    $('#cashout-button').on('click', function() {
        cashOut();
    });

    function spinSlots() {
        $.post('/roll', function(response) {
            stopSpinning(response.symbols, response.credits, response.win, response.reward);
        }).fail(function() {
            $('#message').text('Failed to spin the slots. Please try again.');
        });
    }



    function stopSpinning(symbols, credits, win, reward) {
        setTimeout(function() {
            $('#slot1Symbols .symbol').each(function() {
                $(this).html(symbols[0]).hide().fadeIn(100);
            });
        }, 100);
        setTimeout(function() {
            $('#slot2Symbols .symbol').each(function() {
                $(this).html(symbols[1]).hide().fadeIn(250);
            });
        }, 200);
        setTimeout(function() {
            $('#slot3Symbols .symbol').each(function() {
                $(this).html(symbols[2]).hide().fadeIn(300);
            });
            sessionCredits = credits;
            $('#credits').text('Credits: ' + sessionCredits);
            $('#message').text(win ? 'You won ' + reward + ' credits!' : 'You lost 1 credit.');
            $('#spin-button').prop('disabled', false);
            $('#spin-button').css('background-color', '#007bff');
        }, 350);
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

    function createSymbolElement(symbol) {
        return $('<div>').addClass('symbol').text(symbol);
    }

    let spun = false;
    function spin() {
        if (spun) {
            reset();
        }

        const $slots = $('.slot');
        let completedSlots = 0;

        $slots.each(function(index) {
            const $symbols = $(this).find('.symbols');
            const $symbol = $symbols.find('.symbol');
            const symbolHeight = $symbol.outerHeight();
            const symbolCount = $symbols.children().length;

            $('#slot1Symbols .symbol').html('❓');
            $('#slot2Symbols .symbol').html('❓');
            $('#slot3Symbols .symbol').html('❓');

            const totalDistance = symbolCount * symbolHeight;
            const randomOffset = -Math.floor(Math.random() * (symbolCount - 1) + 1) * symbolHeight;
            $symbols.css('top', `${randomOffset}px`);

            $symbols.one('transitionend', function() {
                completedSlots++;
                if (completedSlots === $slots.length) {
                    spinSlots();
                }
            });
        });

        spun = true;
    }

    function reset() {
        const $slots = $('.slot');

        $slots.each(function() {
            const $symbols = $(this).find('.symbols');
            $symbols.css('transition', 'none');
            $symbols.css('top', '0');
            $symbols[0].offsetHeight; // Force reflow to apply the reset without transition
            $symbols.css('transition', '');
        });
    }

    function init() {
        const $slots = $('.slot');
        $slots.each(function() {
            const $symbols = $(this).find('.symbols');
            for (let i = 0; i < 10; i++) {
                $symbols.append(createSymbolElement('❓'));
            }
        });
    }

});