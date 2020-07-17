//
//  StringExt.swift
//  PassengerApp
//
//  Created by Chirag on 15/03/16.
//  Copyright © 2016 BBCS. All rights reserved.
//

import Foundation

extension String {
    
    func index(from: Int) -> Index {
        return self.index(startIndex, offsetBy: from)
    }
    
    func substring(from: Int) -> String {
        let fromIndex = index(from: from)
        return substring(from: fromIndex)
    }
    
    func substring(to: Int) -> String {
        let toIndex = index(from: to)
        return substring(to: toIndex)
    }
    
    func substring(with r: Range<Int>) -> String {
        let startIndex = index(from: r.lowerBound)
        let endIndex = index(from: r.upperBound)
        return substring(with: startIndex..<endIndex)
    }

    func contains(find: String) -> Bool{
        return self.range(of: find) != nil
    }
    
    func containsIgnoringCase(find: String) -> Bool{
        return self.range(of: find, options: NSString.CompareOptions.caseInsensitive) != nil
    }
    
    func containsOnlyLetters() -> Bool {
        for chr in self.characters {
            if (!(chr >= "a" && chr <= "z") && !(chr >= "A" && chr <= "Z") ) {
                return false
            }
        }
        return true
    }
    
    func isNumeric() -> Bool {
        return Double(self) != nil
    }
    
    func replace(_ target: String, withString: String) -> String
    {
        return self.replacingOccurrences(of: target, with: withString, options: NSString.CompareOptions.literal, range: nil)
    }
    
    var utf8Data: Data? {
        return data(using: String.Encoding.utf8)
    }
    
    func trim() -> String
    {
        return self.trimmingCharacters(in: CharacterSet.whitespacesAndNewlines)
    }
    
    func trimAll() -> String
    {
        return self.replacingOccurrences(of: " ", with: "")
    }
    
    func getJsonDataDict() -> NSDictionary{
        
        let data = self.data(using: String.Encoding.utf8, allowLossyConversion: false)
        
        let dict = [String:String]()
        
        if let jsonData = data {
            // Will return an object or nil if JSON decoding fails
            
            do {
                let jsonData = try JSONSerialization.jsonObject(with: jsonData, options: JSONSerialization.ReadingOptions.mutableContainers)
                
                let jsonDataDict = jsonData as! NSDictionary
                
                return jsonDataDict
            }  catch {
                return dict as NSDictionary
            }
            
        } else {
            return dict as NSDictionary
        }
        
    }
    
    
    
    /// To check if the string contains characters other than white space and \n
    var isBlank: Bool {
        get {
            let trimmed = trimmingCharacters(in: .whitespacesAndNewlines)
            return trimmed.isEmpty
        }
    }
    
    subscript(r: Range<Int>) -> String? {
        get {
            let stringCount = self.characters.count as Int
            if (stringCount < r.upperBound) || (stringCount < r.lowerBound) {
                return nil
            }
            let startIndex = self.characters.index(self.startIndex, offsetBy: r.lowerBound)
            let endIndex = self.characters.index(self.startIndex, offsetBy: r.upperBound - r.lowerBound)
            return self[(startIndex ..< endIndex)]
        }
    }
    
    func containsAlphabets() -> Bool {
        //Checks if all the characters inside the string are alphabets
        let set = CharacterSet.letters
        return self.utf16.contains( where: {
            guard let unicode = UnicodeScalar($0) else { return false }
            return set.contains(unicode)
        })
    }
    
//    subscript(r: Range<Int>) -> String? {
//        get {
//            let stringCount = self.characters.count as Int
//            if (stringCount < r.upperBound) || (stringCount < r.lowerBound) {
//                return nil
//            }
//            let startIndex = self.characters.index(self.startIndex, offsetBy: r.lowerBound)
//            let endIndex = self.characters.index(self.startIndex, offsetBy: r.upperBound - r.lowerBound)
//            return self[(startIndex ..< endIndex)]
//        }
//    }
//    
//    func containsAlphabets() -> Bool {
//        //Checks if all the characters inside the string are alphabets
//        let set = CharacterSet.letters
//        return self.utf16.contains( where: { return set.contains(UnicodeScalar($0)!)  } )
//    }
    subscript (i: Int) -> Character {
        return self[self.characters.index(self.startIndex, offsetBy: i)]
    }
    
    func charAt (i: Int) -> String {
        return String(self[i] as Character)
    }
    
    func getHTMLString(fontName:String, fontSize:String, textColor:String, text:String) -> NSAttributedString{
        
        var direction = "ltr"
        if(Configurations.isRTLMode()){
            direction = "rtl"
        }
        
        let text = text.replacingOccurrences(of: "\n", with: "<br/>")

        let modifiedFont = NSString(format:"<div dir=\"\(direction)\" style=\"font-family: \(fontName); font-size: \(fontSize); color: \(textColor)\">%@</div>" as NSString, text) as String
        
        let attrStr = try! NSAttributedString(
            data: modifiedFont.data(using: String.Encoding.unicode, allowLossyConversion: true)!,
            options: [NSDocumentTypeDocumentAttribute: NSHTMLTextDocumentType, NSCharacterEncodingDocumentAttribute: String.Encoding.utf8.rawValue],
            documentAttributes: nil)
        
        return attrStr
    }
    
    func condenseWhitespace() -> String {
        let components = self.components(separatedBy: NSCharacterSet.whitespacesAndNewlines)
        return components.filter { !$0.isEmpty }.joined(separator: " ")
    }
    
    func height(withConstrainedWidth width: CGFloat, font: UIFont) -> CGFloat {
        let constraintRect = CGSize(width: width, height: .greatestFiniteMagnitude)
        let boundingBox = self.boundingRect(with: constraintRect, options: .usesLineFragmentOrigin, attributes: [NSFontAttributeName: font], context: nil)
        
        return boundingBox.height
    }
    
    func width(withConstrainedHeight height: CGFloat, font: UIFont) -> CGFloat {
        let constraintRect = CGSize(width: .greatestFiniteMagnitude, height: height)
        let boundingBox = self.boundingRect(with: constraintRect, options: .usesLineFragmentOrigin, attributes: [NSFontAttributeName: font], context: nil)
        
        return boundingBox.width
    }
}

extension NSAttributedString {
    func height(withConstrainedWidth width: CGFloat) -> CGFloat {
        let constraintRect = CGSize(width: width, height: .greatestFiniteMagnitude)
        let boundingBox = boundingRect(with: constraintRect, options: .usesLineFragmentOrigin, context: nil)
        
        return boundingBox.height
    }
    
    func width(withConstrainedHeight height: CGFloat) -> CGFloat {
        let constraintRect = CGSize(width: .greatestFiniteMagnitude, height: height)
        let boundingBox = boundingRect(with: constraintRect, options: .usesLineFragmentOrigin, context: nil)
        
        return boundingBox.width
    }
}
