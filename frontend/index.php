<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Online Shopping Web Services</title>
  <link rel="stylesheet" type="text/css" href="/frontend/styles.css" />
</head>

<body>
  <main>
    <h1>Welcome to Online Shopping Web Services!</h1>

    <!-- Set User ID Section -->
    <section class="userIdSection">
      <input type="text" id="userIdInput" placeholder="Set User ID..." />
      <button onclick="setUserId()">Set User ID</button>
    </section>

    <section class="actions">
      <div>
        <button onclick="fetchItems('/seller1/items')">Seller1 Items</button>
        <button onclick="fetchItems('https://seller2-shopping-site.test/items')">Seller2 Items</button>
        <button onclick="showAddBalanceForm()">Add Balance</button>
      </div>
      <div>
        <input type="text" id="searchTerm" placeholder="Search items..." />
        <button onclick="searchItems()">Search</button>
      </div>
      <div>
        <input type="number" id="searchInput" placeholder="User ID or Purchase ID" />
        <select id="searchType">
          <option value="userId">Search by User ID</option>
          <option value="purId">Search by Purchase ID</option>
        </select>
        <button onclick="searchPurchase()">Search Purchase</button>
      </div>
    </section>

    <section id="content">
      <!-- Dynamic Content will be loaded here -->
    </section>
  </main>

  <div id="cardCheckModal" class="modal">
    <div class="modal-content">
      <span class="close-button" onclick="closeCardCheckModal()">
        &times;
      </span>
      <h2>Card Validation</h2>
      <label for="cardNum">Card Number:</label>
      <input type="text" id="cardNum" value="" />
      <label for="pin">PIN:</label>
      <input type="password" id="pin" value="" />
      <button onclick="validateCard()">Validate</button>
    </div>
  </div>

  <script src="/frontend/script.js"></script>
</body>

</html>