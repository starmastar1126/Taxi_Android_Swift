//
//  MyTextField.swift
//  PassengerApp
//
//  Created by NEW MAC on 08/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

protocol MyTxtFieldClickDelegate {
    func myTxtFieldTapped(sender:MyTextField)
}
protocol MyTxtFieldOnTextChangeDelegate {
    func onTextChanged(sender:MyTextField, text:String)
}

class MyTextField: UIView, TextFieldDelegate {

    
    @IBInspectable internal var fontFamilyName:String = "Roboto-Light"
    @IBInspectable internal var fontSize:CGFloat = 16
    @IBInspectable internal var isAppThemeTxtColor:Bool = false
    
    @IBInspectable internal var allColor:UIColor!
   
    var menuRequired = true
    var placeHolder = ""
    var text = ""
    var isEnabled = true
    
    var isDividerEnabled = true
    var isHighLightedEnabled = false
    
    var txtField:ErrorTextField!
    
    var myTxtFieldDelegate:MyTxtFieldClickDelegate?
    var onTextChangedDelegate:MyTxtFieldOnTextChangeDelegate?
    
    var arrowImgView:UIImageView!
    
    var maxCharacterLimit = -1
    
    var textFieldType = ""
    
    var regexToMatch = ""
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        initializeField()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        initializeField()
        
    }
    
    func initializeField(){
        if(txtField == nil){
            self.txtField = ErrorTextField(frame: CGRect(x: 0, y: 0, width: self.width, height: 32))
        }else{
            self.txtField.frame = CGRect(x: 0, y: 0, width: self.frame.width, height: 32)
        }
    }
    func disableMenu() {
        self.menuRequired = false
        
        if(txtField == nil){
            self.txtField = ErrorTextField(frame: CGRect(x: 0, y: 0, width: self.width, height: 32))
        }
        
        self.txtField.isMenuRequired = false
        
    }
    override func layoutSubviews() {
        addTextField()
    }
    
    func addTextField(){
        self.backgroundColor = UIColor.clear
        
        initializeField()
        
//        let txtField = ErrorTextField(frame: CGRect(x: 0, y: 0, width: self.width, height: 32))
//        txtField.frame = self.frame
        txtField.center = CGPoint(x: self.bounds.midX, y: self.bounds.midY)
        if(allColor != nil){
            txtField.dividerNormalColor = allColor
            txtField.dividerActiveColor = allColor
            txtField.placeholderNormalColor = allColor
            txtField.placeholderActiveColor = allColor
            txtField.textColor = allColor
        }else if(isAppThemeTxtColor == false){
            txtField.dividerNormalColor = UIColor.UCAColor.textFieldDividerInActiveColor
            txtField.dividerActiveColor = UIColor.UCAColor.textFieldDividerActiveColor
            txtField.placeholderNormalColor = UIColor.UCAColor.textFieldPlaceholderInActiveColor
            txtField.placeholderActiveColor = UIColor.UCAColor.textFieldPlaceholderActiveColor
        }else{
            txtField.dividerNormalColor = UIColor.UCAColor.AppThemeTxtColor
            txtField.dividerActiveColor = UIColor.UCAColor.AppThemeTxtColor
            txtField.placeholderNormalColor = UIColor.UCAColor.AppThemeTxtColor
            txtField.placeholderActiveColor = UIColor.UCAColor.AppThemeTxtColor
            txtField.textColor = UIColor.UCAColor.AppThemeTxtColor
        }
        
//        txtField.isClearIconButtonEnabled = true
        txtField.placeholder = self.placeHolder
        txtField.text = self.text
        txtField.font = UIFont(name: fontFamilyName, size: fontSize)
        txtField.placeholderLabel.font = UIFont(name: fontFamilyName, size: fontSize)
        txtField.isEnabled = self.isEnabled
        
        txtField.autocapitalizationType = .none
        txtField.autocorrectionType = .no
//        textField.setClearButtonEnabled(isClearButtonEnabled: true)
        
        if(Configurations.isRTLMode()){
            txtField.textAlignment = .right
        }else{
            txtField.textAlignment = .left
        }
        
        txtField.delegate = self
        txtField.addTarget(self, action: #selector(self.textFieldDidChange(textField:)), for: .editingChanged)

        txtField.isDividerHidden = isDividerEnabled == true ? false : true
        
        let tapGue = UITapGestureRecognizer()
        self.isUserInteractionEnabled = true
        tapGue.addTarget(self, action: #selector(self.myTxtFieldClicked(sender:)))
        self.addGestureRecognizer(tapGue)
        
        self.addSubview(txtField)
        
        if(arrowImgView != nil){
            self.addSubview(arrowImgView)
        }
        
        if(isHighLightedEnabled){
            txtField.dividerNormalColor = UIColor.UCAColor.textFieldDividerActiveColor
            txtField.dividerActiveColor = UIColor.UCAColor.textFieldDividerInActiveColor
            
            txtField.placeholderNormalColor = UIColor.UCAColor.textFieldPlaceholderActiveColor
            
            txtField.placeholderActiveColor = UIColor.UCAColor.textFieldPlaceholderInActiveColor
        }
    }
    
    func myTxtFieldClicked(sender:UITapGestureRecognizer){
        myTxtFieldDelegate?.myTxtFieldTapped(sender: self)
    }
    
    func setPlaceHolder(placeHolder:String){
        self.placeHolder = placeHolder
        if(getTextField() != nil){
            getTextField()!.placeholder = placeHolder
        }
    }
    
    func setText(text:String){
        self.text = text
        if(getTextField() != nil){
            getTextField()!.text = text
        }
        if(onTextChangedDelegate != nil){
            onTextChangedDelegate?.onTextChanged(sender: self, text: text)
        }
    }
    
    func setEnable(isEnabled:Bool){
        self.isEnabled = isEnabled
        if(getTextField() != nil){
            getTextField()!.isEnabled = isEnabled
        }
    }
    
    func configDivider(isDividerEnabled:Bool){
        self.isDividerEnabled = isDividerEnabled
        if(getTextField() != nil){
            getTextField()!.isDividerHidden = isDividerEnabled == true ? false : true
        }
    }
    func configHighlighted(isHighLightedEnabled:Bool){
        self.isHighLightedEnabled = isHighLightedEnabled
        if(getTextField() != nil && isHighLightedEnabled){
            txtField.dividerNormalColor = UIColor.UCAColor.textFieldDividerActiveColor
            txtField.dividerActiveColor = UIColor.UCAColor.textFieldDividerInActiveColor
            
            txtField.placeholderNormalColor = UIColor.UCAColor.textFieldPlaceholderActiveColor
            
            txtField.placeholderActiveColor = UIColor.UCAColor.textFieldPlaceholderInActiveColor
        }
    }
    
    func textFieldDidChange(textField: UITextField) {
        self.text = textField.text!
//        if(textField.keyboardType == .numberPad){
//            let text = textField.text!.containsOnlyLetters()
//        }
        getTextField()!.isErrorRevealed = false
        
        if(onTextChangedDelegate != nil){
            onTextChangedDelegate?.onTextChanged(sender: self, text: textField.text!)
        }
    }
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        getTextField()!.isErrorRevealed = false
    }
    
    func textFieldShouldClear(_ textField: UITextField) -> Bool {
        getTextField()!.isErrorRevealed = false
        return true
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
    
    public func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        getTextField()!.isErrorRevealed = false
        
        if(textField.keyboardType == .numberPad || textField.keyboardType == .decimalPad){
            let aSet = NSCharacterSet(charactersIn:textField.keyboardType == .numberPad ? "0123456789" : "0123456789.").inverted
            let compSepByCharInSet = string.components(separatedBy: aSet)
            let numberFiltered = compSepByCharInSet.joined(separator: "")
            
            if(string == numberFiltered && self.maxCharacterLimit != -1 && self.textFieldType != "CARD" ){
                let newLength = textField.text!.characters.count + string.characters.count - range.length
                return newLength <= maxCharacterLimit
            }
            
            if(textField.keyboardType == .decimalPad){
                if(textField.text! == "" && string == "."){
                    textField.text = "0"
                }
                
                if((GeneralFunctions.parseDouble(origValue: -1.00, data: "\(textField.text!)\(string)0")) != -1.00){
                    
                    var dataArr = "\(textField.text!)\(string)".components(separatedBy: ".")
                    
                    if(dataArr.count > 1 && dataArr[1].characters.count > 2 && range.location > dataArr[0].characters.count){
                        return false
                    }
                    
                    return true
                }else{
                    return false
                }
            }
            
            if(self.textFieldType == "CARD"){
                let text = textField.text!.replace(" ", withString: "")
                
                if(self.maxCharacterLimit != -1 ){
                    let newLength = text.characters.count + string.characters.count - range.length
                    if(newLength <= maxCharacterLimit){
                    }else{
                        return false
                    }
                    
                }
                
                
                if((text.characters.count % 4) == 0 && string.isNumeric()){
                    textField.text = textField.text! + " "
                }
                
                if(string == ""){
                    let lstChar = textField.text!.charAt(i: textField.text!.characters.count - 1)
                    if(lstChar == " "){
                        let str = textField.text!
                        textField.text = str.substring(to: str.characters.count - 1)
                    }
                }
            }
            
            return string == numberFiltered
        }
        
        
        
        if(self.maxCharacterLimit != -1 ){
            let newLength = textField.text!.characters.count + string.characters.count - range.length
            return newLength <= maxCharacterLimit
        }
        
        if(regexToMatch != "" && string != ""){
            let regexTest = NSPredicate(format:"SELF MATCHES %@", regexToMatch)
            return regexTest.evaluate(with: string)
        }
        
        return true
    }
    
    
    func addArrowView(color:UIColor, transform:CGAffineTransform){
        
        if(self.arrowImgView != nil){
            return
        }
        
        let arrowImgView = UIImageView()
        arrowImgView.image = UIImage(named: "ic_arrow_right")
        GeneralFunctions.setImgTintColor(imgView: arrowImgView, color: color)
        arrowImgView.transform = transform
        
        if(Configurations.isRTLMode()){
            arrowImgView.frame = CGRect(x: 0 , y: (self.frame.height / 2) - 20, width: 40, height: 40)
        }else{
            arrowImgView.frame = CGRect(x:  self.frame.width - 40 , y: (self.frame.height / 2) - 20, width: 40, height: 40)
        }

        if(self.subviews.count > 0){
            
            self.addSubview(arrowImgView)
        }else{
            self.arrowImgView = arrowImgView
        }
    }
    
    func getTextField() -> ErrorTextField? {
        if(self.subviews.count > 0){
            if(self.subviews[0].isKind(of: ErrorTextField.self)){
                return self.subviews[0] as? ErrorTextField
            }else{
                return self.txtField!
            }
        }else if(self.txtField != nil){
            return self.txtField!
        }else{
            return nil
        }
       
    }
}
