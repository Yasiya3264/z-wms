<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Warehouse Rack Location Entry</title>
  <style>
    input, select {
      margin: 5px;
      padding: 6px;
    }
    label {
      display: inline-block;
      width: 150px;
    }
    .location-block {
      border: 1px solid #ccc;
      margin-top: 10px;
      padding: 10px;
    }
  </style>
</head>
<body>

<h2>Rack Location Generator + DB Insert</h2>

<label>Zone Codes (comma separated):</label><input id="zones" value="A"><br>
<label>Racks per Zone:</label><input type="number" id="racks" value="1"><br>
<label>Positions per Rack:</label><input type="number" id="positions" value="1"><br>
<label>Levels per Position:</label><input type="number" id="levels" value="1"><br>

<label>Length (cm):</label><input type="number" id="length" value="100"><br>
<label>Width (cm):</label><input type="number" id="width" value="100"><br>
<label>Height (cm):</label><input type="number" id="height" value="100"><br>

<label>Occupancy Type:</label>
<select id="occupancy">
  <option value="Actual">Actual</option>
  <option value="Virtual">Virtual</option>
</select><br>

<label>Status:</label>
<input type="checkbox" id="status" checked> Active<br>

<button onclick="generateAndSend()">Generate + Insert</button>

<div id="result"></div>

<script>
function pad(n) {
  return n.toString().padStart(2, '0');
}

function generateAndSend() {
  const zones = document.getElementById("zones").value.split(',').map(z => z.trim());
  const racks = parseInt(document.getElementById("racks").value);
  const positions = parseInt(document.getElementById("positions").value);
  const levels = parseInt(document.getElementById("levels").value);

  const length = parseFloat(document.getElementById("length").value);
  const width = parseFloat(document.getElementById("width").value);
  const height = parseFloat(document.getElementById("height").value);
  const cbm = ((length * width * height) / 1000000).toFixed(4); // in cubic meters

  const occupancy = document.getElementById("occupancy").value;
  const status = document.getElementById("status").checked ? 1 : 0;

  let results = [];

  zones.forEach(zone => {
    for (let r = 1; r <= racks; r++) {
      for (let p = 1; p <= positions; p++) {
        for (let l = 1; l <= levels; l++) {
          const location_code = `R01${zone}-${pad(r)}-${pad(p)}-${pad(l)}`;

          // Send to backend PHP
          fetch('insert_location.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
              location_code,
              length,
              width,
              height,
              cbm,
              occupancy,
              status
            })
          })
          .then(res => res.text())
          .then(data => {
            results.push(`${location_code}: ${data}`);
            document.getElementById("result").innerText = results.join('\n');
          });
        }
      }
    }
  });
}
</script>

</body>
</html>
