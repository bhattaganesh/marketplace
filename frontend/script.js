let currentUserId = null;
let currentToken = null;
const DOMAIN = "";

function setUserId() {
  const userId = document.getElementById("userIdInput").value;
  if (!userId) {
    alert("Please provide a User ID.");
    return;
  }
  currentUserId = userId;
  alert(`User ID set to ${currentUserId}`);
}

function showCardCheckModal(callback, isUserIdRequired = true) {
  if (isUserIdRequired && !currentUserId) {
    alert("Please provide a User ID.");
    return;
  }

  const modal = document.getElementById("cardCheckModal");
  modal.style.display = "block";

  if (callback && typeof callback === "function") {
    modalCallback = callback;
  }
}

function closeCardCheckModal() {
  const modal = document.getElementById("cardCheckModal");
  modal.style.display = "none";
}

function validateCard() {
  const cardNum = document.getElementById("cardNum").value;
  const pin = document.getElementById("pin").value;

  fetch(`${DOMAIN}/card-check`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ cardNum, pin }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        currentToken = data.token;
        closeCardCheckModal();
        if (modalCallback) {
          modalCallback();
          modalCallback = null;
        }
      } else {
        alert(data.message || "Card validation failed.");
      }
    });
}

function fetchItems(seller) {
  const endpoint = seller;

  fetch(endpoint)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayItems(data.data, data.seller_ip);
      } else {
        // alert(data.message);
      }
    });
}

function searchItems() {
  const searchTerm = document.getElementById("searchTerm").value;
  if (!searchTerm) {
    alert("Please provide a search term.");
    return;
  }

  const endpoint = `${DOMAIN}/items?search=${searchTerm}`;

  fetch(endpoint)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayItems(data.items);
      } else {
        alert(data.message);
      }
    });
}

function displayItems(items, seller_ip = null) {
  const contentDiv = document.getElementById("content");

  let html = '<table border="1" cellspacing="0" cellpadding="10">';
  html +=
    "<thead><tr><th>ID</th><th>Name</th><th>Stock Quantity</th><th>Price per Unit</th><th>Action</th></tr></thead><tbody>";

  items.forEach((item) => {
    html += `<tr>
                <td>${item.item_id}</td>
                <td>${item.item_name}</td>
                <td>${item.stock_qty}</td>
                <td>${item.price_of_unit}</td>
                <td><button onclick="purchaseItem('${item.item_id}', '${
      seller_ip ? seller_ip : item.seller_ip
    }')">Purchase</button></td>
            </tr>`;
  });

  html += "</tbody></table>";

  contentDiv.innerHTML = html;
}

function purchaseItem(itemId, sellerIP) {
  const userId = currentUserId;
  if (!userId) {
    alert("Please provide a User ID.");
    return;
  }

  showCardCheckModal(function () {
    if (!currentToken) {
      return;
    }

    const data = {
      token: currentToken,
      itemId: itemId,
      userId: userId,
      quantity: 1,
      seller_ip: sellerIP,
    };

    fetch(`${DOMAIN}/purchase-item`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          alert("Item purchased successfully.");
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error(
          "There was a problem with the fetch operation:",
          error.message
        );
      });
  });
}

function searchPurchase() {
  const searchInput = document.getElementById("searchInput").value;
  const searchType = document.getElementById("searchType").value;

  if (!searchInput) {
    alert("Please provide an input.");
    return;
  }

  let endpoint;

  if (searchType === "userId") {
    endpoint = `${DOMAIN}/search-purchase?userId=${searchInput}`;
  } else {
    endpoint = `${DOMAIN}/search-purchase?purId=${searchInput}`;
  }

  fetch(endpoint)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayPurchases(data.data);
      } else {
        alert(data.message);
      }
    });
}

function displayPurchases(purchases) {
  const contentDiv = document.getElementById("content");

  if (purchases.length === 0) {
    contentDiv.innerHTML = "<p>No purchases found.</p>";
    return;
  }

  let html = '<table border="1" cellspacing="0" cellpadding="10">';
  html +=
    "<thead><tr><th>Purchase ID</th><th>User ID</th><th>Item ID</th><th>Quantity</th><th>Price</th><th>Seller IP</th><th>Date</th><th>Actions</th></tr></thead><tbody>";

  purchases.forEach((purchase) => {
    html += `<tr>
                  <td>${purchase.pur_id}</td>
                  <td>${purchase.user_id}</td>
                  <td>${purchase.item_id}</td>
                  <td>${purchase.quantity}</td>
                  <td>${purchase.price}</td>
                  <td>${purchase.seller_ip}</td>
                  <td>${purchase.date}</td>
                  <td><button onclick="cancelPurchase('${purchase.pur_id}')">Cancel</button></td>
              </tr>`;
  });

  html += "</tbody></table>";

  contentDiv.innerHTML = html;
}

function cancelPurchase(purchaseId) {
  showCardCheckModal(function () {
    if (!currentToken) {
      return;
    }

    const data = {
      token: currentToken,
      purchaseId: purchaseId,
    };

    fetch(`${DOMAIN}/cancel-purchase`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        console.log(data);
        if (data.success) {
          alert("Purchase canceled successfully.");
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error(
          "There was a problem with the fetch operation:",
          error.message
        );
      });
  }, false);
}

function showAddBalanceForm() {
  const contentDiv = document.getElementById("content");
  const userId = currentUserId;

  const html = `
      <h2>Add Balance</h2>
      <label for="userIdInput">User ID:</label>
      <input type="number" id="balanceUserIdInput" value="${userId}" />
      <br><br>
      <label for="balanceAmount">Amount to Add:</label>
      <input type="number" id="balanceAmount">
      <br><br>
      <button onclick="addBalance()">Add</button>
  `;

  contentDiv.innerHTML = html;
}

function addBalance() {
  const userIdInput = document.getElementById("balanceUserIdInput");
  const balanceAmountInput = document.getElementById("balanceAmount");
  const userId = userIdInput.value;
  const amount = parseFloat(balanceAmountInput.value);

  // Validate the user input
  if (!userId) {
    alert("Please provide a User ID.");
    return;
  }

  currentUserId = currentUserId || userId;

  if (isNaN(amount) || amount <= 0) {
    alert("Please provide a valid amount greater than 0.");
    return;
  }

  showCardCheckModal(() => {
    if (!currentToken) {
      return;
    }

    const data = {
      token: currentToken,
      userId: userId,
      amount: amount,
    };

    fetch(`${DOMAIN}/add-balance`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          userIdInput.value = currentUserId;
          balanceAmountInput.value = "";
          alert("Balance added successfully.");
        } else {
          alert(data.message);
        }
      });
  });
}

// Initialize the default view with Seller1's items
fetchItems("/seller1/items");
