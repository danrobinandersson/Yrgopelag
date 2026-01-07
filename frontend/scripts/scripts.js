function calculateNights(arrival, departure) {
  if (!arrival || !departure) return 0;

  const start = new Date(arrival);
  const end = new Date(departure);

  const diffTime = end - start;
  const diffDays = diffTime / (1000 * 60 * 60 * 24);

  return diffDays > 0 ? diffDays : 0;
}

function calculateTotal() {
  let roomPrice = 0;
  let roomTotal = 0;
  let featuresTotal = 0;
  let discountAmount = 0;

  const arrival = document.getElementById("arrival").value;
  const departure = document.getElementById("departure").value;

  const nights = calculateNights(arrival, departure);

  // Check for selected room
  const selectedRoom = document.querySelector('input[name="room"]:checked');
  let roomTier = null;

  if (selectedRoom && nights > 0) {
    roomPrice = Number(selectedRoom.dataset.price);
    roomTier = selectedRoom.value;
    roomTotal = roomPrice * nights;
  }

  // Check for selected features
  const selectedFeatures = [];
  document
    .querySelectorAll('input[name="features[]"]:checked')
    .forEach((feature) => {
      featuresTotal += Number(feature.dataset.price);
      selectedFeatures.push(feature.dataset.category);
    });

  let total = roomTotal + featuresTotal;

  // Package discount
  const hasWater = selectedFeatures.includes("water");
  const hasHotelSpecific = selectedFeatures.includes("hotel-specific");

  if (roomTier === "standard" && hasWater && hasHotelSpecific) {
    discountAmount = total * 0.1;
    total -= discountAmount;

    document.getElementById("discount-line").style.display = "block";
    document.getElementById("discount-amount").textContent =
      discountAmount.toFixed(2);
  } else {
    document.getElementById("discount-line").style.display = "none";
  }

  // Update values in page
  document.getElementById("room-price").textContent = roomPrice.toFixed(2);
  document.getElementById("nights").textContent = nights;
  document.getElementById("features-price").textContent =
    featuresTotal.toFixed(2);
  document.getElementById("total-price").textContent = total.toFixed(2);
}

// Event listeners
document
  .querySelectorAll(
    '#arrival, #departure, input[name="room"], input[name="features[]"]'
  )
  .forEach((input) => {
    input.addEventListener("change", calculateTotal);
  });
