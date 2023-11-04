let apiData = { //Global variable to use data anywhere
    fetchStats: function(account) {
        return fetch( "https://owapi.io/profile/pc/us/"+ account)
  
        .then((response) =>{ //Throws error if account not found
            if(!response.ok){
                console.log("No Account Found For " + account)
                throw new Error("No account found")
            }
            return response.json()
        })
        .then((data) => this.displayStats(data))
        .catch((error => {
          console.error("Error fetching stats: ", error)
        }))
    },
  
    displayStats: function(data){ //Making data useable 
        const { username } = data;
        let tankDivision = "";
        let damageDivision = "";
        let supportDivision = "";
      
        if (data.competitive && data.competitive.tank) {
            // Assign values if the data is available
            tankDivision = data.competitive.tank.rank;
          }
        
          if (data.competitive && data.competitive.offense) {
            damageDivision = data.competitive.offense.rank;
          }
        
          if (data.competitive && data.competitive.support) {
            supportDivision = data.competitive.support.rank;
          }
  
          console.log(username + " Tank Rank: " + tankDivision + " DPS Rank: " + damageDivision + " Support Rank: " + supportDivision);
  
  
    },
  
    searchNames: function(names) {
      const fetchPromises = names.map((name) => { //map function goes through "names" and creates an array of promises
        return this.fetchStats(name)
          .catch((error) => { //catches any errors that occur and shows where error happens
            console.error("Error for name " + name + ":", error);
          });
      });
  
      // Wait for all fetch requests to complete
      Promise.all(fetchPromises)
        .then(() => { //callback function activates when there are no errors
          console.log("All requests completed."); // You can add a message here if needed
        })
        .catch((error) => {
          console.error("Error collecting data:", error);
        });
    },
  };
  
  const namesToSearch = ["JoeGoldberg-3244", "JoeGoldberg-2631", "HerǂSlave-2622", "RobotNinja-1545699", "GrayMoose-15472", "JoeGoldberg-3244" ]
  
  apiData.searchNames(namesToSearch)
  