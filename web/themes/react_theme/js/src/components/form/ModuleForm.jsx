import React, { useState, useEffect } from "react";

//Components
import Loader from "../utils/Loader";
import Modal from "../utils/Modal";
//styles
import "../../../../scss/components/_module-form.scss"

const ModuleForm = () => {
    const [formData, setFormData] = useState({
        fullName: "",
        email: "",
        demandRequest: "",
        requirements:"",
        acceptanceCriteria:"",
        businessValue:"",
        consequence:"",
        date:"",
        dependencies:"",
        businessRequestor: "",
        file:""
    });
    //State
    const [showForm, setShowForm] = useState(false);
    const [errors, setErrors] = useState();
    const [loading, setLoader] = useState(false);
    


    const handleChange = (e) => {
        const {name, value} = e.target;
        setFormData((prevFormData) => ({...prevFormData, [name]:value}))
    }
    const isValidEmail = (email) => {
        const emailRegex = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        return emailRegex.test(email)
    }
    const validateForm = () => {
        let newErrors = {}
        if(!formData.fullName){
            newErrors.fullName = "Your fullname is required"
        }
        if(!formData.email){
            newErrors.email = "Email is required"
        }else if(!isValidEmail(formData.email)){
            newErrors.email = "Invalid email format"
        }
        if(
            !formData.demandRequest || 
            !formData.requirements || 
            !formData.acceptanceCriteria ||
            !formData.businessValue ||
            !formData.consequence ||
            !formData.date ||
            !formData.dependencies ||
            !formData.businessRequestor ||
            !formData.file
        ){
            newErrors.fieldError = "This field is required"
        }
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    }
    const handleSubmit = (e) => {
        setLoader(true);
        e.preventDefault();
        const isValid = validateForm()
        if(isValid){

            console.log("is valid form");

        }else{
            console.log("error");
        }
    }
    const modalText = {
        title: "Thank you!",
        text:"Your request has been submitted successfully. A Takeda Medical information representative will contact you within 2 to 3 business days. Some requests may take longer depending on complexity.",
        textBtn:"Go back Home"
    }

    


    return(
        <>
            <form 
                className="module-form__form" 
                >
                <div className="module-form__input-group">
                    <div>
                        <label>Full Name<span>*</span>:</label>
                        <input 
                            type="text" 
                            name="fullName"
                            value={formData.fullName}
                            placeholder="Enter your full name"
                            onChange={() => handleChange}
                        />
                    </div>
                    <div>
                        <label>Takeda email<span>*</span>:</label>
                        <input 
                            type="text" 
                            name="email"
                            value={formData.email}
                            placeholder="Enter your email"
                            onChange={() => handleChange}
                        />
                    </div>
                    
                </div>
                <label>Demand Request Name/Initiative Name<span>*</span></label>
                <input 
                    type="text" 
                    name="demandRequest"
                    value={formData.demandRequest}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Please describe your demand requirements (provide as many as possible details)<span>*</span>:</label>
                <input 
                    type="text" 
                    name="requirements"
                    value={formData.requirements}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Please Provide Acceptance criteria<span>*</span>:</label>
                <input 
                    type="text" 
                    name="acceptanceCriteria"
                    value={formData.acceptanceCriteria}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Business Value (describe the value this request is enabling for the LOC, BU, Region,etc)<span>*</span>:</label>
                <input 
                    type="text" 
                    name="businessValue"
                    value={formData.businessValue}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Business Value (describe the value this request is enabling for the LOC, BU, Region,etc)<span>*</span>:</label>
                <input 
                    type="text" 
                    name="businessValue"
                    value={formData.businessValue}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Consequence of not doing the request<span>*</span>:</label>
                <input 
                    type="text" 
                    name="consequence"
                    value={formData.consequence}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Please enter desired demand delivery date<span>*</span>:</label>
                <input 
                    type="date" 
                    name="date"
                    value={formData.date}
                    placeholder="Enter delivery date"
                    onChange={() => handleChange}
                />
                <label>Please specify any dependencies to other Initiatives, Campaigns and Product Launches etc. driving the Desired GO LIVE DATE for this demand<span>*</span>:</label>
                <input 
                    type="text" 
                    name="dependencies"
                    value={formData.dependencies}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Please enter the Business Requestor/Demand Requestor (who will be supporting the Product Owner if needed in product backlog refinement sessions)<span>*</span>:</label>
                <input 
                    type="text" 
                    name="businessRequestor"
                    value={formData.businessRequestor}
                    placeholder="Enter your answer"
                    onChange={() => handleChange}
                />
                <label>Please upload any documents ( solution design, product roadmap, flow diagram etc)<span>*</span>:</label>
                <input 
                    type="file" 
                    name="file"
                    value={formData.file}
                    onChange={() => handleChange}
                    className="module-form__input-file"

                />
                <div className="module-form__btns">
                    <button 
                        onClick={ handleSubmit }
                        className="module-form__request">
                        Request module
                    </button>
                    <button
        
                        className="module-form__back"
                    >
                        Back to configuration 
                    </button>

                </div>
            </form>
            {
            loading &&
            <div className="module-form__loader">
                <Loader />
            </div>
           
            }
            {
                !loading &&
                <Modal
                    title={modalText.title}
                    text={modalText.text}
                    author={modalText.textBtn}
                />

            }

        </>
    )
}
export default ModuleForm;
