document.addEventListener("DOMContentLoaded", async () => {
    const transactionForm = document.getElementById("transaction-form");
    const cryptoDropdown = document.getElementById("crypto-name");
    const otherCryptoInput = document.getElementById("other-crypto-name");
    const pricesList = document.getElementById("crypto-prices-list");
    const recommendationForm = document.getElementById("recommendation-form");
    const recommendationResult = document.getElementById("recommendation-result");

    const cryptoSymbols = ["BTC", "ETH", "ADA", "SOL", "BNB", "XRP", "DOGE", "XLM", "HBAR", "TRX", "AVAX"]; // Cryptocurrencies to track

    async function checkAuthentication() {
        try {
            const response = await fetch("/api/auth");
            if (response.status !== 200) {
                window.location.href = "/login";
                return false;
            }
            return true;
        } catch (error) {
            console.error("Error checking authentication:", error);
            window.location.href = "/login";
            return false;
        }
    }

    async function fetchLivePrices() {
        try {
            const response = await fetch(`/api/crypto-prices?symbols=${cryptoSymbols.join(",")}`);
            if (!response.ok) {
                throw new Error("Failed to fetch cryptocurrency prices.");
            }
            const data = await response.json();
            const prices = data.data;
            if (!prices) throw new Error("No data received from the crypto API.");

            pricesList.innerHTML = Object.keys(prices)
                .map((symbol) => {
                    const price = prices[symbol]?.quote?.USD?.price?.toFixed(2) || "N/A";
                    return `
                        <li>
                            <span>${symbol}</span>
                            <span>$${price}</span>
                        </li>
                    `;
                })
                .join("");
        } catch (error) {
            console.error("Error fetching live prices:", error);
            pricesList.innerHTML = `<li>Error loading prices. Please refresh the page.</li>`;
        }
    }

    recommendationForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const userInput = document.getElementById("recommendation-input").value.trim();
        if (!userInput) {
            recommendationResult.innerHTML = `<p>Please enter a valid question.</p>`;
            return;
        }

        try {
            const response = await fetch("/api/chatgpt", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ question: userInput }),
            });
            const data = await response.json();
            recommendationResult.innerHTML = data.recommendation
                ? `<p>${data.recommendation}</p>`
                : `<p>Sorry, no recommendation could be generated.</p>`;
        } catch (error) {
            console.error("Error fetching recommendation:", error);
            recommendationResult.innerHTML = `<p>Failed to retrieve recommendation. Please try again later.</p>`;
        }
    });

    cryptoDropdown.addEventListener("change", () => {
        if (cryptoDropdown.value === "other") {
            otherCryptoInput.style.display = "block";
            otherCryptoInput.required = true;
        } else {
            otherCryptoInput.style.display = "none";
            otherCryptoInput.required = false;
        }
    });

    transactionForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        let cryptoName = cryptoDropdown.value;
        if (cryptoName === "other") cryptoName = otherCryptoInput.value.trim();

        const cryptoQuantity = parseFloat(document.getElementById("crypto-quantity").value);
        const cryptoPrice = parseFloat(document.getElementById("crypto-price").value);

        if (!cryptoName || isNaN(cryptoQuantity) || isNaN(cryptoPrice)) {
            alert("Please fill out all fields with valid data.");
            return;
        }

        try {
            const response = await fetch("/api/transactions", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    cryptocurrency: cryptoName,
                    quantity: cryptoQuantity,
                    price: cryptoPrice,
                }),
            });
            if (response.ok) {
                alert("Transaction added successfully.");
                window.location.href = "/transactions";
            } else {
                const errorData = await response.json();
                alert(errorData.message || "Failed to add transaction.");
            }
        } catch (error) {
            console.error("Error adding transaction:", error);
            alert("An error occurred while adding the transaction. Please try again.");
        }
    });

    if (await checkAuthentication()) {
        fetchLivePrices();
        setInterval(fetchLivePrices, 30000);
    }
});
