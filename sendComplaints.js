const cron = require('node-cron');
const mysql = require('mysql2');
const nodemailer = require('nodemailer');
const corn = require('node-cron');



// Define the connection parameters
const connection = mysql.createConnection({
  host: 'vultr-prod-cfbaea5c-14b5-4c87-b3e2-9929eed22e05-vultr-prod-4489.vultrdb.com',
  user: 'vultradmin',
  password: 'special_password',
  database: 'defaultdb',
  port: 16751
});

// Connect to the database
connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err.message);
    return;
  }
  console.log('Connected to the MySQL database as id ' + connection.threadId);
});


function generateData() {
    return new Promise((resolve, reject) => {
      connection.query("SELECT * FROM model_complaints", (err, res) => {
        if (err) {
          console.log(err.message);
          reject(err);
        } else {
            // return res;
          resolve(res);
        }
      });
    });
  }

//   generateData();

  async function generateTable() {
      let data = await generateData();  // Use await to get the result of the query
    //   console.log(data);  // Log the data or generate a table with it
  
  
      // Generate an HTML table with the fetched data
      let tableHTML = `<table border="1">
        <thead>
            <tr>
                <th>Model complaint id</th>
                <th>Complaint Id</th>
                <th>User Id</th>
                <th>Description</th>
                <th>Low Priority</th>
                <th>Medium Priority</th>
                <th>High Priority</th>
                <th>Complain Time</th>
            </tr>
        </thead>
        <tbody>`;
  
      for (d of data){
        tableHTML += `
            <tr>
                <td>${d.model_complaint_id}</td>
                <td>${d.complaint_id}</td>
                <td>${d.user_id}</td>
                <td>${d.description}</td>
                <td>${d.low_priority}</td>
                <td>${d.medium_priority}</td>
                <td>${d.high_priority}</td>
                <td>${d.created_at}</td>
            </tr>`;  // Adjust column names as needed
      };
  
      tableHTML += `</tbody></table>`;
    //   console.log(tableHTML);  // Output the HTML table

    await sendMail(tableHTML);
  };

  async function sendMail(tableHTML){
    const transporter = nodemailer.createTransport({
        service: 'gmail',
        auth: {
            user: 'singhutkarshkumar097@gmail.com',
            pass: 'xuuc fnbv ohvn puze'  //app password
        },
    });

    const mailToBeSent = {
        from: 'singhutkarshkumar097@gmail.com',
        to: 'utkarshk313@gmail.com',
        subject: "Yesterday's Complaints" ,
        html: `
          <h1>Yesterday's Complaints</h1>
          ${tableHTML}
        `
      }
    
      transporter.sendMail(mailToBeSent, (error, info)=>{
        if(error){
          console.log("error occured", error);
        }
        else{
          console.log("email sent", info.response);
        }
      })
}


cron.schedule('*/5 * * * * *', async () => {
    await generateTable();
});