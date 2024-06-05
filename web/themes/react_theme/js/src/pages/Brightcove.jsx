import React, { useState, useEffect } from "react";
import Loader from "../components/utils/Loader";

//styles
import "../../../scss/components/_module-info.scss"
const BrightcovePage = () =>{
  const [nodeData, setData] = useState(null);
  const [firstInfo, setFirstInfo] = useState();
  const [showForm, setShowForm] = useState(false);

  const fetchData = async () => {
    try{
      const response = await fetch ("http://common-modules-demo.docksal.site/node/45?_format=json")

      const jsonData = await response.json();
      console.log(jsonData, "data, bright");
      setData(jsonData);
      setFirstInfo(jsonData.field_fields[0].value)
      
    }catch (err){
      console.error(err, "error to fetch data")
      
    }
  }
  useEffect(() =>{
    fetchData()
    
     
  },[])
  return(
    <>
      <section className="module-info">
        {
          nodeData ? 
          (
            <div className="module-info__pg" >
              <h2>
                {nodeData.field_module_title[0].value}
              </h2>
              {!showForm && <div className="module-info__content">
                <div 
                  dangerouslySetInnerHTML={{__html:nodeData.field_fields[0].value}}
                  className="module-info__fields"
                >
                </div>
                <div 
                  dangerouslySetInnerHTML={{__html:nodeData.field_f[0].value}}
                  className="module-info__fn-cookie">
                </div>
                <div 
                   
                  dangerouslySetInnerHTML={{__html:nodeData.field_configuration[0].value}}
                  className="module-info__config"
                >
                  

                </div>

              </div>}
              
            </div>

          ):(

            <Loader />
          )
        }
      </section>

    </>
  )
}
export default BrightcovePage;