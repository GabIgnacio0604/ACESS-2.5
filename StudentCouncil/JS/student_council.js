// Original inline script for BOTH User and StudentCouncil Student Council pages
// Place this code inside <script> tags at the bottom of the HTML files

document.addEventListener("DOMContentLoaded", async () => {
  const nameElement = document.querySelector(".account-name");
  const executiveContainer = document.getElementById("executive-positions");
  const gradeContainer = document.getElementById("grade-representatives");
  const voteLeadersContainer = document.getElementById("vote-leaders");

  // Fetch user name
  try {
    const res = await fetch("../Admin/Php/get_logged_user.php");
    const data = await res.json();

    if (data.fullname) {
      nameElement.textContent = data.fullname;
    } else {
      nameElement.textContent = "Not Logged In";
      console.error("Error fetching name:", data.error);
    }
  } catch (err) {
    console.error("Fetch failed:", err);
    nameElement.textContent = "Error";
  }

  // Load elected candidates
  async function loadElectedCandidates() {
    try {
      const response = await fetch('../Admin/Php/get_elected_candidates.php');
      const data = await response.json();

      if (data.success) {
        // Display executive positions
        if (data.executive && data.executive.length > 0) {
          executiveContainer.innerHTML = '';
          data.executive.forEach(candidate => {
            const positionDiv = document.createElement('div');
            positionDiv.className = 'position';
            
            const photoHtml = candidate.photo_url 
              ? `<img src="${candidate.photo_url}" alt="${candidate.name}" style="width:100%;height:200px;object-fit:cover;border-radius:10px;">`
              : `<div class="candidate-card" style="height:200px;display:flex;align-items:center;justify-content:center;font-size:3rem;">👤</div>`;
            
            positionDiv.innerHTML = `
              <h3>${candidate.position}</h3>
              <p>${candidate.name}</p>
              ${photoHtml}
              <p style="color:#ffd54f;margin-top:10px;">Votes: ${candidate.votes}</p>
            `;
            executiveContainer.appendChild(positionDiv);
          });
        } else {
          executiveContainer.innerHTML = '<p style="text-align:center;color:#999;">No executive positions filled yet.</p>';
        }

        // Display grade representatives
        if (data.grades && data.grades.length > 0) {
          gradeContainer.innerHTML = '';
          data.grades.forEach(candidate => {
            const gradeCard = document.createElement('div');
            gradeCard.className = 'grade-card';
            
            const photoHtml = candidate.photo_url 
              ? `<img src="${candidate.photo_url}" alt="${candidate.name}" style="width:100%;height:150px;object-fit:cover;border-radius:8px;margin-top:10px;">`
              : `<div class="candidate-card" style="height:150px;margin-top:10px;">👤</div>`;
            
            gradeCard.innerHTML = `
              ${candidate.position}<br>
              <strong>${candidate.name}</strong>
              ${photoHtml}
              <p style="margin-top:10px;font-size:0.9rem;">Votes: ${candidate.votes}</p>
            `;
            gradeContainer.appendChild(gradeCard);
          });
        } else {
          gradeContainer.innerHTML = '<p style="text-align:center;color:#999;grid-column:1/-1;">No grade representatives yet.</p>';
        }

        // Display top 2 vote leaders (President candidates typically)
        if (data.executive && data.executive.length > 0) {
          const topCandidates = [...data.executive]
            .sort((a, b) => b.votes - a.votes)
            .slice(0, 2);

          voteLeadersContainer.innerHTML = '';
          topCandidates.forEach(candidate => {
            const voteCard = document.createElement('div');
            voteCard.className = 'vote-card';
            
            const photoHtml = candidate.photo_url 
              ? `<img src="${candidate.photo_url}" alt="${candidate.name}" style="width:100%;height:150px;object-fit:cover;border-radius:8px;">`
              : `<div class="candidate-card" style="height:150px;">👤</div>`;
            
            voteCard.innerHTML = `
              <p><strong>${candidate.name}</strong></p>
              <p style="font-size:0.9rem;color:#ccc;">${candidate.position}</p>
              ${photoHtml}
              <p class="vote-count">${candidate.votes} votes</p>
            `;
            voteLeadersContainer.appendChild(voteCard);
          });
        } else {
          voteLeadersContainer.innerHTML = '<p style="text-align:center;color:#999;">No votes cast yet.</p>';
        }

      } else {
        console.error('Error loading candidates:', data.message);
        executiveContainer.innerHTML = '<p style="color:#f44;">Error loading data</p>';
        gradeContainer.innerHTML = '<p style="color:#f44;">Error loading data</p>';
        voteLeadersContainer.innerHTML = '<p style="color:#f44;">Error loading data</p>';
      }
    } catch (err) {
      console.error('Error:', err);
      executiveContainer.innerHTML = '<p style="color:#f44;">Failed to load data</p>';
      gradeContainer.innerHTML = '<p style="color:#f44;">Failed to load data</p>';
      voteLeadersContainer.innerHTML = '<p style="color:#f44;">Failed to load data</p>';
    }
  }

  // Load the data
  loadElectedCandidates();
});