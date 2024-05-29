import React, { useState, useEffect } from "react";

//components
import ModuleForm from "../components/form/ModuleForm";
import Loader from "../components/utils/Loader";

//styles
import "../../../scss/components/_module-info.scss"

const DeeplinkPage = () => {
  const [nodeData, setData] = useState(null);
  const [fieldData, setFieldData] = useState([]);
  const [firstInfo, setFirstInfo] = useState();
  const [showForm, setShowForm] = useState(false);
  const fetchData = async () =>{
    try{
        const response = await fetch("http://common-modules-demo.docksal.site/jsonapi/node/module_content")  
        if(response.ok){

            const jsonData = await response.json()
            console.log(jsonData, "data")
            setData(jsonData.data[1].attributes);
            setFieldData(jsonData.data[1].attributes.field_f)
            setFirstInfo(jsonData.data[1].attributes.field_fields.value)


        }else{
            console.log("error");
        }

    }catch(err){
        console.error("error to fetch data")

    }
  }
  
  const fieldList = fieldData.map((item) => {
   return item.value
  })
  
  


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
                {nodeData.field_module_title}
              </h2>
              {!showForm && <div className="module-info__content">
                <div 
                  dangerouslySetInnerHTML={{__html:firstInfo}}
                  className="module-info__fields"
                >
                </div>
                <div 
                  dangerouslySetInnerHTML={{__html:fieldList}}
                  className="module-info__fn">
                </div>

              </div>}
              {!showForm && <div className="module-info__div">
                <div className="module-info__division"></div>
                <button  
                    onClick={() => setShowForm(true)}      
                    className="module-info__show-form"
                >
                    Request a module
                </button>
              </div>}
              
              {showForm && <ModuleForm/>}
            </div>

          ):(

            <Loader />
          )
        }
      </section>
    </>
  )


}

export default DeeplinkPage;