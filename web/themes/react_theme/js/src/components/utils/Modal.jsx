import React from "react";
import Folder from  "../../../../assets/images/folder.svg"
import "../../../../scss/components/_popup-modal.scss"

const Modal = (props) => {
  return(
    <>
      <section className="popup-modal">
        <div className="popup-modal__container">
          <img src={Folder} alt="folder" />
          <div className="popup-modal__text">
            <h2 className="popup-modal__title">
              {props.title}
            </h2>
            <p>
              {props.text}
            </p>
          </div>
          <button className="popup-modal__btn">
            {props.textBtn}
          </button>
        </div>
      </section>
    </>
  )
}

export default Modal;