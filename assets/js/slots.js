$(document).ready(function() {
    let sessionCredits = 0;
    let spun = false;

    // Initialize the game
    initializeGame();

    // Event handlers
    $('#spin-button').on('click', handleSpin);
    $('#cashout-button').on('click', handleCashOut);

    // Functions

    function initializeGame() {
        fetchInitialCredits();
        initSlots();
    }

    function fetchInitialCredits() {
        $.get('/start')
            .done(response => {
                sessionCredits = response.credits;
                updateCreditsDisplay();
            })
            .fail(() => {
                displayMessage('Failed to get the initial credits. Please try again.');
            });
    }

    function handleSpin() {
        if (sessionCredits === 0) {
            displayMessage('You do not have enough credits to spin the slots.');
            return;
        }

        $('#spin-button').prop('disabled', true).css('background-color', 'grey');
        $('#cashout-button').prop('disabled', true).css('background-color', 'grey');

        if (spun) {
            resetSlots();
        }

        spinSlots();
        spun = true;
    }

    function handleCashOut() {
        $.post('/cashout')
            .done(response => {
                sessionCredits = 0;
                updateCreditsDisplay();
                displayMessage('You cashed out ' + response.cashed_out + ' credits.');
            })
            .fail(() => {
                displayMessage('Failed to cash out. Please try again.');
            });
    }

    function spinSlots() {
        const $slots = $('.slot');
        let completedSlots = 0;

        $slots.each(function(index) {
            const $symbols = $(this).find('.symbols');
            const randomOffset = getRandomOffset($symbols);
            resetSlotSymbols();

            $symbols.css('top', `${randomOffset}px`).one('transitionend', function() {
                completedSlots++;
                if (completedSlots === $slots.length) {
                    triggerServerSpin();
                }
            });
        });
    }

    function triggerServerSpin() {
        $.post('/roll')
            .done(response => {
                updateSlots(response.symbols);
                updateCredits(response.credits);
                displayResult(response.win, response.reward);
            })
            .fail(() => {
                displayMessage('Failed to spin the slots. Please try again.');
            })
            .always(() => {
                $('#spin-button').prop('disabled', false).css('background-color', '#007bff');
                $('#cashout-button').prop('disabled', false).css('background-color', '#007bff');
            });
    }

    function updateSlots(symbols) {
        setTimeout(() => updateSlot('#slot1Symbols', symbols[0]), 100);
        setTimeout(() => updateSlot('#slot2Symbols', symbols[1]), 250);
        setTimeout(() => updateSlot('#slot3Symbols', symbols[2]), 400);
    }

    function updateSlot(selector, symbol) {
        $(selector).find('.symbol').each(function() {
            $(this).html(symbol).hide().fadeIn(100);
        });
    }

    function resetSlots() {
        $('.slot .symbols').css({
            'transition': 'none',
            'top': '0'
        }).each(function() {
            this.offsetHeight; // Force reflow to apply the reset without transition
            $(this).css('transition', '');
        });
    }

    function initSlots() {
        const $slots = $('.slot');
        $slots.each(function() {
            const $symbols = $(this).find('.symbols');
            for (let i = 0; i < 10; i++) {
                $symbols.append(createSymbolElement('❓'));
            }
        });
    }

    function resetSlotSymbols() {
        $('#slot1Symbols .symbol, #slot2Symbols .symbol, #slot3Symbols .symbol').html('❓');
    }

    function getRandomOffset($symbols) {
        const symbolHeight = $symbols.find('.symbol').outerHeight();
        const symbolCount = $symbols.children().length;
        return -Math.floor(Math.random() * (symbolCount - 1) + 1) * symbolHeight;
    }

    function createSymbolElement(symbol) {
        return $('<div>').addClass('symbol').text(symbol);
    }

    function updateCredits(newCredits) {
        sessionCredits = newCredits;
        updateCreditsDisplay();
    }

    function updateCreditsDisplay() {
        $('#credits').text('Credits: ' + sessionCredits);
    }

    function displayMessage(message) {
        $('#message').text(message);
    }

    function displayResult(win, reward) {
        const resultMessage = win ? `You won ${reward} credits!` : 'You lost 1 credit.';
        displayMessage(resultMessage);
    }
});
