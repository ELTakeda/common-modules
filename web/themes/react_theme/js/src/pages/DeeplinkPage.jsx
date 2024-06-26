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
        const response = await fetch("http://common-modules-demo.docksal.site/node/42?_format=json")  
        if(response.ok){

            const jsonData = await response.json()
            console.log(jsonData, "data")
            setData(jsonData);
            setFieldData(jsonData.field_f)
            setFirstInfo(jsonData.field_fields[0].value)


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

  const closeForm = (e) =>{
    e.preventDefault()
    setShowForm(false);
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
                  dangerouslySetInnerHTML={{__html:firstInfo}}
                  className="module-info__fields"
                >
                </div>
                <div 
                  dangerouslySetInnerHTML={{__html:fieldList}}
                  className="module-info__fn">
                </div>
                <div className="module-info__field-btn">
                  <button 
                    className="module-info__exp-btn"
                    dangerouslySetInnerHTML={{__html:nodeData.field_example[0].value}}></button>
                  <button  
                    className="module-info__exp-btn"
                    dangerouslySetInnerHTML={{__html:nodeData.field_example[1].value}}>

                  </button>

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
              
              {showForm && 
                <ModuleForm
                  closeForm={closeForm}
                />}
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