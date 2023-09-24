let currentUserId = null;
let currentToken = null;

function setUserId() {
  const userId = document.getElementById("userIdInput").value;
  if (!userId) {
    alert("Please provide a User ID.");
    return;
  }
  currentUserId = userId;
  alert(`User ID set to ${currentUserId}`);
}

function showCardCheckModal() {
  const modal = document.getElementById("cardCheckModal");
  modal.style.display = "block";
}

function closeCardCheckModal() {
  const modal = document.getElementById("cardCheckModal");
  modal.style.display = "none";
}

function validateCard() {
  const cardNum = document.getElementById("cardNum").value;
  const pin = document.getElementById("pin").value;

  fetch("https://marketplace.test/card-check", {
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
      } else {
        alert(data.message || "Card validation failed.");
      }
    });
}

function fetchItems(seller) {
  const endpoint = `https://marketplace.test/${seller}/items`;

  fetch(endpoint)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayItems(data.data);
      } else {
        alert(data.message);
      }
    });
}

function searchItems() {
  const searchTerm = document.getElementById("searchTerm").value;
  if (!searchTerm) {
    alert("Please provide a search term.");
    return;
  }

  const endpoint = `https://marketplace.test/items?search=${searchTerm}`;

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

function displayItems(items) {
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
                <td><button onclick="purchaseItem('${item.item_id}')">Purchase</button></td>
            </tr>`;
  });

  html += "</tbody></table>";

  contentDiv.innerHTML = html;
}

function purchaseItem(itemId) {
  const userId = currentUserId;
  if (!userId) {
    alert("Please provide a User ID.");
    return;
  }

  showCardCheckModal();

  const data = {
    token: currentToken,
    itemId: itemId,
    userId: userId,
    quantity: 1,
    seller: "Seller1",
  };

  fetch("https://marketplace.test/purchase-item", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Item purchased successfully.");
      } else {
        alert(data.message);
      }
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
    endpoint = `https://marketplace.test/search-purchase?userId=${searchInput}`;
  } else {
    endpoint = `https://marketplace.test/search-purchase?purId=${searchInput}`;
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
    "<thead><tr><th>Purchase ID</th><th>User ID</th><th>Item ID</th><th>Quantity</th><th>Price</th><th>Seller</th><th>Date</th></tr></thead><tbody>";

  purchases.forEach((purchase) => {
    html += `<tr>
                  <td>${purchase.pur_id}</td>
                  <td>${purchase.user_id}</td>
                  <td>${purchase.item_id}</td>
                  <td>${purchase.quantity}</td>
                  <td>${purchase.price}</td>
                  <td>${purchase.seller_id}</td>
                  <td>${purchase.date}</td>
              </tr>`;
  });

  html += "</tbody></table>";

  contentDiv.innerHTML = html;
}

function showAddBalanceForm() {
  const contentDiv = document.getElementById("content");
  const userId = currentUserId;

  const html = `
      <h2>Add Balance</h2>
      <label for="userIdInput">User ID:</label>
      <input type="number" id="userIdInput" value="${userId}" />
      <br><br>
      <label for="balanceAmount">Amount to Add:</label>
      <input type="number" id="balanceAmount">
      <br><br>
      <button onclick="addBalance()">Add</button>
  `;

  contentDiv.innerHTML = html;
}

function addBalance() {
  const userId = document.getElementById("userIdInput").value;
  const amount = document.getElementById("balanceAmount").value;

  showCardCheckModal();

  const data = {
    token: currentToken,
    userId: userId,
    amount: amount,
  };

  fetch("https://marketplace.test/add-balance", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Balance added successfully.");
      } else {
        alert(data.message);
      }
    });
}

// Initialize the default view with Seller1's items
fetchItems("seller1");
