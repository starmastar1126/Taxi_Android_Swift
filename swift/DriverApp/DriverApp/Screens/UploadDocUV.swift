//
//  UploadDocUV.swift
//  DriverApp
//
//  Created by NEW MAC on 03/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class UploadDocUV: UIViewController, MyBtnClickDelegate, MyTxtFieldClickDelegate, MyLabelClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var docHLbl: MyLabel!
    @IBOutlet weak var expDateTxtField: MyTextField!
    @IBOutlet weak var takePhotoImgView: UIImageView!
    @IBOutlet weak var continueBtn: MyButton!
    @IBOutlet weak var docSelectedImgBgView: UIView!
    @IBOutlet weak var docSelectedCheckImgView: UIImageView!
    
    let generalFunc = GeneralFunctions()
    
    let photoImgTapGue = UITapGestureRecognizer()
    
    var dataDict:NSDictionary!
    
    var openImgSelection:OpenImageSelectionOption!
    
    var listOfDocumentUv:ListOfDocumentUV!
    
    var DOC_TYPE = "driver"
    var iDriverVehicleId = ""
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "UploadDocScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
    }
    

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Upload Document", key: "LBL_UPLOAD_DOC")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Upload Document", key: "LBL_UPLOAD_DOC")
        
        self.docHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Select Document", key: "LBL_SELECT_DOC")
//        LBL_TAKE_PHOTO_OR_SELECT
        
        photoImgTapGue.addTarget(self, action: #selector(self.photoImgTapped))
        takePhotoImgView.isUserInteractionEnabled = true
        takePhotoImgView.addGestureRecognizer(photoImgTapGue)
        
        if(self.dataDict.get("ex_status") == "yes"){
            expDateTxtField.isHidden = false
            self.expDateTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Expiry Date", key: "LBL_EXPIRY_DATE"))
            if(self.dataDict.get("ex_date") != ""){
                self.expDateTxtField.setText(text: self.dataDict.get("ex_date"))
            }
        }else{
            expDateTxtField.isHidden = true
        }
        
        self.continueBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_SUBMIT_TXT"))
        self.continueBtn.clickDelegate = self
//        LBL_CONTINUE_BTN
        
        
        self.docHLbl.setClickDelegate(clickDelegate: self)
        
        self.expDateTxtField.setEnable(isEnabled: false)
        self.expDateTxtField.myTxtFieldDelegate = self
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            self.expDateTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
            
        })
        
        docSelectedImgBgView.backgroundColor = UIColor.UCAColor.AppThemeTxtColor
        GeneralFunctions.setImgTintColor(imgView: docSelectedCheckImgView, color: UIColor.UCAColor.AppThemeColor)
        
        docSelectedImgBgView.isHidden = true
        docSelectedCheckImgView.isHidden = true
        
        if(self.dataDict.get("doc_file") != ""){
            self.docSelectedImgBgView.isHidden = false
            self.docSelectedCheckImgView.isHidden = false
            self.takePhotoImgView.image = UIImage(named: "dummy_user_card_alpha")
        }
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.docHLbl){
            photoImgTapped()
        }
    }
    
    func myTxtFieldTapped(sender: MyTextField) {
        let minDate = Calendar(identifier: Configurations.getCalendarIdentifire()).date(byAdding: .hour, value: 1, to: Date())
        if(sender == self.expDateTxtField){
            DatePickerDialog().show(self.generalFunc.getLanguageLabel(origValue: "Expiry Date", key: "LBL_EXPIRY_DATE"), doneButtonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_TXT"), cancelButtonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), minimumDate: minDate, datePickerMode: .date) {
                (date) -> Void in
                
                if(date != nil){
                    let dateFormatter = DateFormatter()
                    dateFormatter.locale = Locale(identifier: "en_GB")
                    dateFormatter.dateFormat = "YYYY-MM-dd"
                    let dateString = dateFormatter.string(from: date!)
                    
                    self.expDateTxtField.setText(text: dateString)
                }
                
            }
        }
    }
    
    func photoImgTapped(){
        
        self.openImgSelection = OpenImageSelectionOption(uv: self)
        self.openImgSelection.isDocumentUpload = true
        self.openImgSelection.setImageSelectionHandler { (isImageSelected) in
            if(isImageSelected){
                self.docSelectedImgBgView.isHidden = false
                self.docSelectedCheckImgView.isHidden = false
                self.takePhotoImgView.image = UIImage(named: "dummy_user_card_alpha")
            }
        }
        self.openImgSelection.show { (isImageUpload) in
            if(isImageUpload == true){
                
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Your document is uploaded successfully", key: "LBL_UPLOAD_DOC_SUCCESS"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    self.performSegue(withIdentifier: "unwindToDocumentList", sender: self)

                })
            }
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.continueBtn){
            
            if(self.dataDict.get("ex_status") == "yes"){
                
                if(Utils.checkText(textField: self.expDateTxtField.getTextField()!) == false){
                    Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "Expiry date is required.", key: "LBL_EXP_DATE_REQUIRED"), uv: self)
                    return
                }
            }
            
            if(self.dataDict.get("doc_file") == "" && (self.openImgSelection == nil || self.openImgSelection.selectedImage == nil)){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "Please attach your document.", key: "LBL_SELECT_DOC_ERROR"), uv: self)
                return
            }
            
            let parameters = [
                "type"  : "uploaddrivedocument",
                "MemberType"    : Utils.appUserType,
                "iMemberId"    : GeneralFunctions.getMemberd(),
                "doc_usertype": self.DOC_TYPE,
                "doc_masterid": "\(self.dataDict.get("masterid"))",
                "doc_name": "\(self.dataDict.get("doc_name"))",
                "doc_id": "\(self.dataDict.get("doc_id"))",
                "iDriverVehicleId": "\(self.iDriverVehicleId)",
                "doc_file":"\(self.openImgSelection == nil ? self.dataDict.get("doc_file") : (self.openImgSelection.selectedImage == nil ? self.dataDict.get("doc_file") : ""))",
                "ex_date": self.dataDict.get("ex_status") == "yes" ? Utils.getText(textField: self.expDateTxtField.getTextField()!) : ""
                
            ]
            if(self.openImgSelection == nil){
                self.openImgSelection = OpenImageSelectionOption(uv: self)
                self.openImgSelection.isDocumentUpload = true
                self.openImgSelection.selectedImage = UIImage(named: "ic_trans")
                self.openImgSelection.setImageUploadHandler(imageUploadCompletionHandler: { (isImageUpload) in
                    if(isImageUpload == true){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Your document is uploaded successfully", key: "LBL_UPLOAD_DOC_SUCCESS"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            self.performSegue(withIdentifier: "unwindToDocumentList", sender: self)
                            
                        })
                    }
                })
            }
            self.openImgSelection.setDataParams(dict_data: parameters)
            self.openImgSelection.myDocUploadRequest(image: self.openImgSelection.selectedImage)
        }
    }

}
